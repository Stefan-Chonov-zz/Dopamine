<?php

use App\Core\Response;

class NotFound
{
    public function index()
    {
        return Response::response([ 'error' => 'Route not found' ]);
    }
}