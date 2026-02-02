<?php
namespace App\Services\Sap;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

class SapSessionService
{
    private Client $http;
    private CookieJar $cookies;

    public function __construct()
    {
        $this->cookies = new CookieJar();
        $this->http = new Client([
            'base_uri' => rtrim(config('services.sap_sl.base'), '/') . '/',
            'cookies'  => $this->cookies,
            'verify'   => filter_var(config('services.sap_sl.verify_ssl', false), FILTER_VALIDATE_BOOL),
            'timeout'  => 30,
        ]);
    }

    public function login(): void
    {
        $this->http->post('Login', ['json' => [
            'CompanyDB' => config('services.sap_sl.company'),
            'UserName'  => config('services.sap_sl.user'),
            'Password'  => config('services.sap_sl.pass'),
        ]]);
    }

    public function request(string $method, string $uri, array $options = [])
    {
        if (!$this->hasSession()) $this->login();
        $options['headers']['Accept'] = 'application/json';
        if (strtoupper($method) !== 'GET') {
            $options['headers']['Content-Type'] = 'application/json';
        }
        return $this->http->request($method, ltrim($uri, '/'), $options);
    }

    private function hasSession(): bool
    {
        foreach ($this->cookies->toArray() as $c) {
            if ($c['Name'] === 'B1SESSION') return true;
        }
        return false;
    }
}
