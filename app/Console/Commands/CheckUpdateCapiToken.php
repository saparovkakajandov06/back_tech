<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Console\Command;
use App\CapiTokens;
use App\Mail\CapiError;

class CheckUpdateCapiToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cu_capi {update_from_env?}';

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $app_id = config('app.FB_APP_ID');
        $secret_key = config('app.FB_SECRET_KEY');

        if(!$app_id || !$secret_key)
            return 0;

        $update_from_env = boolval($this->argument('update_from_env'));
        $meta_token = config('app.META_TOKEN');
        if(!$update_from_env && $token = CapiTokens::select('updated_at', 'access_token')->orderBy('id', 'DESC')->first()) {
            $meta_token = $token['access_token'];
        }
        $response = Http::withOptions([
            'force_ip_resolve' => 'v6',
        ])->asForm()->get("https://graph.facebook.com/oauth/access_token", [
            'grant_type'        => 'fb_exchange_token',
            'client_id'         => $app_id,
            'client_secret'     => $secret_key,
            'fb_exchange_token' => $meta_token
        ])->json();
        if(!isset($response['access_token']) || empty($new_token = $response['access_token'])){
            $emails = explode(',', config('logging.emails'));
            foreach ($emails as $key => $email) {
                Mail::to($email)->send(new CapiError());
            }
            return 1;
        }
        CapiTokens::updateOrCreate(
            ['id' => 1],
            ['access_token' => $new_token]
        );
        print("Old token: $meta_token, new token: $new_token");
        return 0;
    }
}


?>