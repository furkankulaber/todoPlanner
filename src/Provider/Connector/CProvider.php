<?php

namespace App\Provider\Connector;
use App\Provider\Traits\RequestTrait;

class CProvider
{
    private $url;
    private $method;
    use RequestTrait;

    public function __construct($url, $method)
    {
        $this->url = $url;
        $this->method = $method;
    }

    public function fetchData() {
        $response = $this->handleRequest(
            $this->url,
            $this->method
        );
        if($response->getStatusCode() !== 200)
        {
            return null;
        }
        return $response->toArray();
    }

}
