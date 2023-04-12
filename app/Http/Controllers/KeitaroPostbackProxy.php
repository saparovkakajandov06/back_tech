<?php

namespace App\Http\Controllers;

use App\Responses\ApiSuccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KeitaroPostbackProxy extends Controller
{
    public function post(Request $request)
    {
        $url = config('payment-systems.keitaroPostbackUrl');
        $data = $request->all();

        foreach ($data as &$value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }
        }

        $response = Http::asForm()->post($url, $data)->body();

        Log::channel('payments')
            ->debug('Keitaro postback is successfully sended', [
                'r' => $response,
                'data' => $data,
                'url' => $url
            ]);

        return new ApiSuccess('ok', ['response' => $response]);
    }
}
