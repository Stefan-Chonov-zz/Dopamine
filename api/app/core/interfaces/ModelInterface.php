<?php

namespace App\Core\Interfaces;

interface ModelInterface
{
    function create($data);
    function get($data = []);
    function update($data);
    function delete($data);
}