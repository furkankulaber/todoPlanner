<?php

namespace App\Provider;

use App\Entity\Provider;

interface ProviderInterface
{
    public function getData(Provider $provider);
}
