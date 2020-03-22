<?php

namespace App\Core;

use App\Core\Helpers\ResponseFormat;
use App\Core\Responses\CsvResponse;
use App\Core\Responses\JsonResponse;
use App\Core\Responses\XmlResponse;

class Response
{
    /**
     * @param array $response
     * @param string $responseFormat
     * @return string
     */
    public static function response($response = [], $responseFormat = '')
    {
        $responseFormat = strtoupper(trim($responseFormat));
        $strategy = new Strategy(new JsonResponse());
        $contentType = 'application/json';
        switch ($responseFormat) {
            case ResponseFormat::XML:
                $contentType = 'application/xml';
                $strategy->setStrategy(new XmlResponse());
                break;
            case ResponseFormat::CSV:
                $contentType = 'text/csv';
                $strategy->setStrategy(new CsvResponse());
                break;
            case ResponseFormat::JSON: break;
            default:
                $defaultResponseFormat = env('DEFAULT_RESPONSE_FORMAT');
                if (isset($defaultResponseFormat) && !empty($defaultResponseFormat)) {
                    return static::response($response, $defaultResponseFormat);
                }
                break;
        }

        header("Content-Type: $contentType");
        return $strategy->execute($response);
    }
}