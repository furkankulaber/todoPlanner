<?php

namespace App\Provider\Adapters;

use App\Entity\Provider;
use App\Provider\ProviderInterface;
use App\Provider\Connector\CProvider;

class Provider1Adapter implements ProviderInterface
{

    public function getData(Provider $provider)
    {
        $provider = new CProvider($provider->getUrl(), $provider->getMethod());
        $dataResponse = $provider->fetchData();
        if ($dataResponse == null) {
            return null;
        }
        return $this->dataMask($dataResponse);
    }

    private function dataMask($datas)
    {
        $returnArray = [];
        foreach ($datas as $data) {
            $returnArray[] = [
                'todoId' => $data['id'],
                'level' => $data['zorluk'],
                'estimated' => $data['sure']
            ];

        }
        return $returnArray;
    }
}
