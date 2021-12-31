<?php

namespace App\Provider\Adapters;

use App\Entity\Provider;
use App\Provider\Connector\CProvider;
use App\Provider\ProviderInterface;

class Provider2Adapter implements ProviderInterface
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
        foreach ($datas as $data)
        {
            foreach ($data as $index => $dd)
            {
                $returnArray[] = [
                    'todoId' => $index,
                    'level' => $dd['level'],
                    'estimated' => $dd['estimated_duration']
                ];
            }
        }
        return $returnArray;
    }
}
