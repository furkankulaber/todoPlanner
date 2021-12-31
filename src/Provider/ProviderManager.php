<?php

namespace App\Provider;

class ProviderManager
{
    public function getProviderData($provider, $data)
    {
        return $provider->getData($data);
    }

}
