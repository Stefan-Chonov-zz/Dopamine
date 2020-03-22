<?php

namespace App\Core\Responses;

use App\Core\Interfaces\StrategyInterface;
use App\Core\DataConverter;

class XmlResponse implements StrategyInterface
{
    /**
     * @param array $data
     * @return string
     */
    public function execute($data = [])
    {
        if (!empty($data)) {
            return DataConverter::toXml($data);
        }
        
        return '';
    }
}