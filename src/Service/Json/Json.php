<?php

namespace App\Service\Json;

use Psr\Log\LoggerInterface;
use League\Csv\Reader;
use League\Csv\Statement;

/**
 * Class Json
 * @package App\Service\Json
 */
class Json
{
    public function getArray(string $jsonFile)
    {
        $data = file_get_contents('data/' . $jsonFile);

        return $this->cleanDataJson(json_decode($data, 1));
    }

    private function cleanDataJson(array $data)
    {
        $resultado = [];

        foreach ($data['categories'] as $key => $categorie) {
            $resultado[$key] = str_replace([' '], '', trim($categorie));
        }

        return $resultado;
    }
}
