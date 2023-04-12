<?php

namespace App\PaymentSystems;

class PaymentSystemProxy
{
    protected string $url;
    protected string $cipher;
    protected string $key;
    protected string $iv;

    public function __construct(
        string $url,
        string $cipher,
        string $key,
        string $iv,
    ) {
        $this->url = $url;
        $this->cipher = $cipher;
        $this->key = $key;
        $this->iv = $iv;
    }

    public function getEncryptedUrl(string $url): string
    {
        $encryptedUrl = base64url_encode(openssl_encrypt($url, $this->cipher, $this->key, 0, $this->iv));
        return $this->url . '/?algo=base&decode=1&to=' . $encryptedUrl;
    }
}
