<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\BatchUpdateSpreadsheetRequest;
use Google\Service\Sheets\BatchUpdateValuesRequest;
use Google\Service\Sheets\ValueRange;

class LoginStats
{
    protected Client $client;
    protected Sheets $sheets;
    protected string $spreadsheetId;
    protected string $title;
    protected int $sheetId;

    public function __construct(string $spreadsheetId)
    {
        $this->spreadsheetId = $spreadsheetId;
        $this->client = new Client();
        $this->client->useApplicationDefaultCredentials();
        $this->client->addScope([
            'https://www.googleapis.com/auth/drive',
            'https://spreadsheets.google.com/feeds',
        ]);
        $this->sheets = new Sheets($this->client);
        $this->title = Str::lower(Carbon::now()->format('F/y'));
    }

    public function dataUp()
    {
        if (!$this->sheetExists() && $this->createSheet()) {
            $this->createFirstRow();
            $this->addEmptyRows();
        }
        $this->insertData();
    }

    private function sheetExists(): bool
    {
        $sheetInfo = $this->sheets->spreadsheets->get($this->spreadsheetId);
        $allsheet_info = $sheetInfo['sheets'];
        $idCats = array_column($allsheet_info, 'properties');
        foreach ($idCats as $item) {
            $sheetTitle = $item['title'] === $this->title ?? false;
        }
        return boolval($sheetTitle);
    }

    private function createSheet()
    {
        $body = new BatchUpdateSpreadsheetRequest([
            'requests' => [
                'addSheet' => [
                    'properties' => [
                        'title' => $this->title
                    ],
                ],
            ],
        ]);

        $res = $this->sheets->spreadsheets->batchUpdate($this->spreadsheetId, $body);
        $this->sheetId = $res?->replies[0]->addSheet->properties->sheetId;
        
        return $this->spreadsheetId === $res->spreadsheetId;
    }

    private function insertData()
    {
        $result = $this->sheets->spreadsheets_values->batchGet($this->spreadsheetId, ['ranges' => "{$this->title}!A1:H"]);
        $res = $result->getValueRanges();
        $idCats = array_column($res, 'values')[0];
        $row = $idCats !== null ? count($idCats) : 0;

        $cacheKeys = cache()->getMemcached()->getAllKeys();
        foreach ($cacheKeys as $key) {
            $validKey = $this->isValidKey($key);
            if (!$validKey) {
                continue;
            }
            $row++;
            $dataCache = Cache::get($validKey);
            $body = new BatchUpdateValuesRequest([
                'valueInputOption' => 'USER_ENTERED',
                'data' => [
                    new ValueRange([
                        'range' => "{$this->title}!A{$row}:H{$row}",
                        'values' => [[
                            $dataCache['date']    ?? 'null',
                            $dataCache['user']    ?? 'null',
                            $dataCache['country'] ?? 'null',
                            $dataCache['lang']    ?? 'null',
                            $dataCache['login']   ?? 'null',
                            $dataCache['site']    ?? 'null',
                            $dataCache['success'] ?? 'null',
                            $dataCache['scraper'] ?? 'null'
                        ]],
                    ]),
                ],
            ]);
            if ($this->sheets->spreadsheets_values->batchUpdate($this->spreadsheetId, $body)) {
                Cache::forget($validKey);
            }

            //limit 60 request per minute
            sleep(1);
        }

        return true;
    }

    public function put(Request $request, string $scraperName, bool $scraperSuccessed = false)
    {
        $pattern = '/^([\w]|[\w](?!.*?\.{2})[\w.]{0,28}[\w])$/';
        $res = preg_match($pattern, $request->login);

        if(!$res){
            return;
        }

        $pattern = "/(App\\\Scraper\\\Simple)\\\([a-zA-Z0-9]+)/";

        preg_match($pattern, $scraperName, $matches);
        [ $full, $path, $scraper ] = $matches;

        $data = [
            'date'    => Carbon::now()->format('d.m.y H:i'),
            'user'    => maybe_user()?->email ?? $request->ip(),
            'country' => $request->country_value,
            'lang'    => $request->cookie('i18n_redirected'),
            'login'   => $request->login,
            'site'    => config('app.url'),
            'success' => $scraperSuccessed,
            'scraper' => $scraper ?? null,
        ];

        $key = "login_{$request->login}:{$data['user']}";

        Cache::put($key, $data, 60*60*48);
    }

    private function isValidKey($key)
    {
        $pattern = "/^([a-zA-Z0-9._]+):(login_[a-zA-Z0-9._]+):([a-zA-Z0-9._@-]+)$/";

        if (preg_match($pattern, $key, $matches) === 1) {
            [ $full, $subKey, $firstKey, $secondKey] = $matches;
            return "{$firstKey}:{$secondKey}";
        } else {
            return false;
        }
    }

    private function createFirstRow()
    {
        return $this->sheets->spreadsheets_values->batchUpdate(
            $this->spreadsheetId,
            new BatchUpdateValuesRequest([
                'valueInputOption' => 'USER_ENTERED',
                'data' => [
                    new ValueRange([
                        'range'  => "{$this->title}!A1:H1",
                        'values' => [[
                            'Дата',
                            'Пользователь',
                            'Страна',
                            'Язык интерфейса',
                            'Что ввел на странице авторизации?',
                            'Сайт',
                            'Успешная авторизация?',
                            'Скрейпер',
                        ]],
                    ])
                ],
            ])
        );
    }

    public function addEmptyRows(int $rowCount = 9000)
    {
        return $this->sheets->spreadsheets->batchUpdate(
            $this->spreadsheetId,
            new BatchUpdateSpreadsheetRequest([
                'requests' => [
                    'appendDimension' => [
                        'sheetId' => $this->sheetId,
                        'dimension' => 'ROWS',
                        'length' => $rowCount,
                    ]  
                ]
            ])
        );
    }
}
