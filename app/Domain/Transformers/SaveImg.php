<?php

namespace App\Domain\Transformers;

use App\UserService;
use Illuminate\Support\Facades\Http;
use Throwable;
use Illuminate\Support\Facades\Log;

class SaveImg implements ITransformer
{
    public function transform(array $input, UserService $us): array
    {
        return array_map(function ($params) use ($us) {

            $img = data_get($params, 'sdata.img');

            if (!empty($img)) {
                try {
                    $savedUrl = Http::timeout(5)->post(env('IMG_PROXY_URL') . '/api/save', [
                        'url' => $img,
                    ])->json('ok');
                }
                catch (Throwable $e) {
                    Log::warning(describe_exception($e));
                    $savedUrl = '/img/default.png';
                }

                // save to /img/abc.jpeg - service path
                // get it by /api/img/abc.jpeg - api path
                data_set($params, 'sdata.img_saved', '/api' . $savedUrl);
            }

            return $params;
        },

        $input);
    }
}
