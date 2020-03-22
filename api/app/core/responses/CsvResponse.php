<?php

namespace App\Core\Responses;

use App\Core\Interfaces\StrategyInterface;
use App\Core\DataConverter;

class CsvResponse implements StrategyInterface
{
    /**
     * @param array $data
     * @return string
     */
    public function execute($data = [])
    {
        return DataConverter::toCsvString($data);
    }
}