<?php

namespace App\Service\Csv;

use Psr\Log\LoggerInterface;
use League\Csv\Reader;
use League\Csv\Statement;

/**
 * Class Csv
 * @package App\Service\Csv
 */
class Csv
{
    public function getArray(string $csvFile)
    {
        $resultado = [];
        $csv = Reader::createFromPath('data/' . $csvFile);
        $csv->setDelimiter(';');

        $stmt = (new Statement());
        $records = $stmt->process($csv);

        foreach ($records as $record) {
            $resultado[] = [
                'PRODUCT' => trim($record[0]),
                'CATEGORY' => trim($record[1]),
                'COST' => $this->cleanData($record[2]),
                'QUANTITY' => $this->cleanData(trim($record[3])),
            ];
        }
        unset($resultado[0]);

        return $resultado;
    }

    private function cleanData($data)
    {
        return str_replace([','], '.', str_replace([' ', '€', '.'], '', trim($data)));
    }
}
