<?php

namespace App\Service\Calculo;

use Psr\Log\LoggerInterface;
use League\Csv\Reader;
use League\Csv\Statement;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\Csv\Csv;
use App\Service\Json\Json;

/**
 * Class Calculo
 * @package App\Service\Calculo
 */
class Calculo
{

    /**
     * @var Csv $csvService
     */
    protected $csvService;

    /**
     * @var Json $jsonService
     */
    protected $jsonService;

    public function calcula(string $csvFile, string $jsonFile)
    {
        $resultado = [];

        $datosCsv = $this->csvService->getArray($csvFile);
        $datosJson = $this->jsonService->getArray($jsonFile);
        $resultado = $this->realizaCalculo($datosCsv, $datosJson);

        return new JsonResponse($resultado);
    }

    private function realizaCalculo($datosCsv, $datosJson)
    {
        foreach ($datosCsv as $datosProducto) {
            if (array_key_exists($datosProducto['CATEGORY'], $datosJson)) {
                $resultado[$datosProducto['CATEGORY']][] = $this->getBeneficio($datosProducto, $datosJson[$datosProducto['CATEGORY']]);
            } else {
                $resultado[$datosProducto['CATEGORY']][] = $this->getBeneficio($datosProducto, $datosJson['*']);
            }
        }
        foreach ($resultado as $key => $res) {
            $beneficio[$key] = array_sum($res);
        }

        return $beneficio;
    }

    private function getBeneficio($datosProducto, $datosBeneficio)
    {
        $resultado = 0;
        $euros = 0;
        $porcentaje = 0;

        preg_match_all('/([-+]?\w.?\w?(€|%))/', $datosBeneficio, $salidaArray);
        for ($i = 0; $i < count($salidaArray[0]); $i++) {
            switch ($salidaArray[2][$i]) {
                case '€':
                    $euros = $this->calculaEuros($resultado, $datosProducto, $salidaArray[0][$i]);
                    break;
                case '%':
                    $porcentaje = $this->calculaPorcentaje($resultado, $datosProducto, $salidaArray[0][$i]);
                    break;
            }
        }
        return ($porcentaje + $euros) * (float) $datosProducto['QUANTITY'];
    }

    private function calculaEuros(&$resultado, $datosProducto, $datosEuros)
    {
        $operador = substr($datosEuros, 0, 1);
        switch ($operador) {
            case '+':
                return (float) str_replace(['+', '€'], '', $datosEuros);
                break;
            case '-':
                return  -(float) str_replace(['+', '€'], '', $datosEuros);
                break;
        }
    }

    private function calculaPorcentaje(&$resultado, $datosProducto, $datosPorcentaje)
    {
        $operador = substr($datosPorcentaje, 0, 1);
        switch ($operador) {
            case '+':
                return (((float) str_replace(['+', '%'], '', $datosPorcentaje) / 100) * (float) $datosProducto['COST']);
                break;
            case '-':
                return - (((float) str_replace(['+', '%'], '', $datosPorcentaje) / 100) * (float) $datosProducto['COST']);
                break;
        }
    }

    /**
     * @param Csv $csvService
     * @required
     */
    public function setCsv(Csv $csvService)
    {
        $this->csvService = $csvService;
    }

    /**
     * @param Json $jsonService
     * @required
     */
    public function setJson(Json $jsonService)
    {
        $this->jsonService = $jsonService;
    }
}
