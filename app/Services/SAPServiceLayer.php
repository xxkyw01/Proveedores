<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Client\RequestException;

class SAPServiceLayer
{
    protected Client $http;
    protected CookieJar $jar;

    public function __construct()
    {
        $cfg = config('sapb1sl');

        $this->jar = new CookieJar();
        $this->http = new \GuzzleHttp\Client([
            'base_uri' => rtrim(config('sapb1sl.base_uri'), '/') . '/',
            'verify'   => config('sapb1sl.verify_ssl'),
            'cookies'  => true,
        ]);
    }

    protected function ensureLogin(): void
    {

        foreach ($this->jar->toArray() as $c) {
            if ($c['Name'] === 'B1SESSION') return;
        }

        if ($cookie = Session::get('sap_sl_cookie')) {
            foreach ($cookie as $c) $this->jar->setCookie($c);
        }

        $cfg = config('sapb1sl');
        $res = $this->http->post('Login', ['json' => [
            'CompanyDB' => $cfg['company'],
            'UserName'  => $cfg['username'],
            'Password'  => $cfg['password'],
        ]]);

        Session::put('sap_sl_cookie', $this->jar->toArray());
    }

    public function request(string $method, string $uri, array $options = [])
    {
        $this->ensureLogin();
        $options['headers']['Accept'] = 'application/json';
        if (strtoupper($method) !== 'GET') {
            $options['headers']['Content-Type'] = 'application/json';
        }
        return $this->http->request($method, ltrim($uri, '/'), $options);
    }
}
