<?php

namespace App\Core\Interfaces;

interface LogInterface
{
    function info(string $message);
    function warning(string $message);
    function error(string $message);
    function debug(string $message);
    function critical(string $message);
}