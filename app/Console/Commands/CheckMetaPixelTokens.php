<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CheckMetaPixelTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'smm:check_metapixel_tokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check MetaPixel Tokens';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    protected string $proxyAccess;
	
    public function __construct()
    {
	$this->proxyAccess = config('app.FBA_PROXY');
        parent::__construct();
    }

    public function parseEnv() {
        $domains = [];

        foreach ($_ENV as $key => $value) {
            if (str_contains($key, 'META_PIXEL_')) {
                $values = explode('|', $value);
                $domains[str_replace('META_PIXEL_', '', $key)] = [
                    'pixel_id' => $values[0] ?? "",
                    'access_token' => $values[1] ?? "",
                ];
            }
        }

        /*
         * Returns [
         *      "domain" => [
         *          "pixel_id" => string,
         *          "access_token" => string,
         *      ]
         * ]
         * */
        return $domains;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $domains = $this->parseEnv();

        foreach ($domains as $domain => $pixelData) {
            $errors = [];

            if (!$pixelData['pixel_id']) {
                $errors[] = 'No pixel id';
            }

            if (!$pixelData['access_token']) {
                $errors[] = 'No access_token';
            }

            if (count($errors)) {
                $this->error("ERROR: $domain " . implode(", ", $errors));
                continue;
            }
			
	    $requestOptions = [];

            if($this->proxyAccess){
                $requestOptions['proxy'] = $this->proxyAccess;
            }else{
		$requestOptions['force_ip_resolve'] = 'v6';
	    }

            $res = Http::withOptions($requestOptions)
                ->asForm()
                ->get(
                    "https://graph.facebook.com/v15.0/" . $pixelData['pixel_id'],
                    [
                        'access_token' => $pixelData['access_token'],
                    ]
                );

            if (!$res->successful() || array_key_exists('error', $res->json())) {
                $this->error("ERROR: $domain error facebook pixel request");
                $this->warn("RESPONSE: \n" . json_encode($res->json(), JSON_PRETTY_PRINT));
                continue;
            }

            $this->info("OK: $domain");
        }

        return 0;
    }
}
