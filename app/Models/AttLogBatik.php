<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttLogBatik extends Model
{
    protected $connection = 'mysql';
    protected $table = 'att_log';
    protected $fillable = ['sn', 'scan_date', 'pin', 'verifymode', 'inoutmode', 'reserved', 'work_code', 'att_id'];

    public $timestamps = false;
}
