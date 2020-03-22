<?php

namespace App\Core;

use App\Core\Helpers\LogLevel;
use App\Core\Interfaces\LogInterface;

class Log implements LogInterface
{
    private $logFilePath;
    private static $instance;

    public static function getInstance($logFilePath) {

        if (is_null(self::$instance)) {
            self::$instance = new Log($logFilePath);
        }

        return self::$instance;
    }

    /**
     * Log constructor.
     * @param string $logFilePath
     */
    protected function __construct($logFilePath)
    {
        $this->logFilePath = $logFilePath;
    }

    /**
     * Save the info to a log
     * @param $message - Message to write in a log
     */
    public function info($message)
    {
        $this->saveMessageToLog($message, LogLevel::INFO);
    }

    /**
     * Save the warning to a log
     * @param $message - Message to write in a log
     */
    public function warning($message)
    {
        $this->saveMessageToLog($message, LogLevel::WARNING);
    }

    /**
     * Save the error to a log
     * @param string $message - Message to write in a log
     */
    public function error($message)
    {
        $this->saveMessageToLog($message, LogLevel::ERROR);
    }

    /**
     * Save the debug to a log
     * @param string $message - Message to write in a log
     */
    public function debug(string $message)
    {
        $this->saveMessageToLog($message, LogLevel::DEBUG);
    }

    /**
     * Save the critical to a log
     * @param string $message - Message to write in a log
     */
    public function critical(string $message)
    {
        $this->saveMessageToLog($message, LogLevel::CRITICAL);
    }

    /**
     * Write message to a log
     * @param string $message - Message to write in a log
     * @param string $logLevel
     */
    private function saveMessageToLog($message, $logLevel)
    {
        if(!file_exists($this->logFilePath)) {
            if (!file_exists(dirname($this->logFilePath)) && !mkdir(dirname($this->logFilePath), 0777, true)) {
                die('Failed to create folders...');
            }

            file_put_contents($this->logFilePath, '');
        }

        $message = '[' . date("Y-m-d H:i:s") . '] [' . $logLevel . '] ' . $message . PHP_EOL;
        error_log($message, 3, $this->logFilePath);
    }
}