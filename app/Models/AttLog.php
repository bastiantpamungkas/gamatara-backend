<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttLog extends Model
{
    protected $connection = 'mysql';
    protected $table = 'att_logs';
    protected $guarded = ['id'];
}
