<?php

namespace App\Models;

use App\Core\DB;
use App\Core\Model;

class News extends Model
{
    public function __construct()
    {
        parent::__construct(DB::getMySqlInstance());
    }
}