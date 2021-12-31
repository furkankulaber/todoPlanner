<?php

namespace App\Provider\Traits;


use Symfony\Component\HttpClient\HttpClient;

trait RequestTrait
{
    private function handleRequest($url, $method = 'GET')
    {
        return HttpClient::create()->request(
            $method,
            $url
        );
    }


}
