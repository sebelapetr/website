<?php

namespace App\Model\Services\Connectors;

use Contributte\Http\Curl\CurlClient;
use Curl\Curl;

class TestConnector
{
    private string $username;
    private string $password;

    public function __construct(string $username, string $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    public function getConnection(): Curl
    {
        $curl = new Curl();
        $curl->setBasicAuthentication($this->username, $this->password);
        $curl->setUrl("https://test.test/");
        $curl->setTimeout(120);
        $curl->setConnectTimeout(120);
        return $curl;
    }
}