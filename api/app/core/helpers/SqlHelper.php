<?php

namespace App\Core\Helpers;

class SqlHelper
{
    /**
     * @param array $parameters
     * @return array
     */
    public function prepareParameters($parameters)
    {
        $result = [];
        foreach ($parameters as $key => $value) {
            $result[':' . $key] = htmlspecialchars(trim($value));
        }

        return $result;
    }

    /**
     * @param array $parameters
     * @param string $prefix
     * @param string $suffix
     * @return array
     */
    public function prepareAliases($parameters, $prefix = '', $suffix = '')
    {
        $result = [];
        $index = 0 ;
        foreach ($parameters as $key => $value) {
            $result[$key] = '';
            if (!empty($prefix) && $index < count($parameters) - 1) {
                $result[$key] .= $prefix;
            }

            $specialKeys = [ 'key', 'id' ];
            if (key_exists($key, $specialKeys)) {
                $result[$key] .= '`' . $key . '` = :' . $key;
            } else {
                $result[$key] .= $key . ' = :' . $key;
            }

            if (!empty($suffix) && $index < count($parameters) - 1) {
                $result[$key] .= $suffix;
            }
            $index++;
        }

        return $result;
    }
}