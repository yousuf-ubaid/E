<?php

class Cryptor
{

    protected $method = 'AES-128-CTR'; // default
    private $key;
    private $secret_iv = 'fb68e879fab1db2a2ce30dbf6f9b3743';
    private $iv;

    public function __construct($key = false, $method = false)
    {
        $this->key = hash('sha256', $key);
        $this->iv = substr(hash('sha256', $this->secret_iv), 0, 16);
    }

    public function encrypt($data)
    {
        $encrypted_string = openssl_encrypt($data, $this->method, $this->key, 0, $this->iv);
        return $encrypted_string;
    }

    // decrypt encrypted string
    public function decrypt($data)
    {
        $decrypted_string = openssl_decrypt($data, $this->method, $this->key, 0, $this->iv);
        return $decrypted_string;
    }

}