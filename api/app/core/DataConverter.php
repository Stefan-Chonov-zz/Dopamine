<?php

namespace App\Core;

class DataConverter
{
    /**
     * @param $data
     * @return false|string
     */
    public static function toJson($data)
    {
        return json_encode($data);
    }

    /**
     * Convert a multi-dimensional, associative array to XML data
     * @param $array
     * @param null $rootElement
     * @param null $xml
     * @return string
     */
    public static function toXml($array, $rootElement = null, $xml = null) {
        $_xml = $xml;
        if ($_xml === null) {
            $_xml = new \SimpleXMLElement($rootElement !== null ? $rootElement : '<root/>');
        }

        foreach ($array as $k => $v) {
            if (is_array($v)) {
                self::toXml($v, $k, $_xml->addChild($k));
            } else {
                $_xml->addChild($k, $v);
            }
        }

        return $_xml->asXML();
    }

    /**
     * Convert a multi-dimensional, associative array to CSV data
     * @param  array $data
     * @return string
     */
    public static function toCsvString($data)
    {
        $fileHandler = fopen('php://temp', 'rw');
        fputcsv($fileHandler, array_keys(current($data)));
        foreach ($data as $row) {
            fputcsv($fileHandler, $row);
        }
        rewind($fileHandler);
        $csv = stream_get_contents($fileHandler);
        fclose($fileHandler);

        return $csv;
    }
}