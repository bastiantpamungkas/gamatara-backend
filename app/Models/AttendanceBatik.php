<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceBatik extends Model
{
    protected $connection = 'mysql';
    protected $table = 'attendances';
    protected $fillable = ['id', 'user_id', 'name', 'pin', 'time_check_in', 'time_check_out', 'created_at', 'updated_at', 'status', 'shift_id'];

    public $timestamps = false;
    public $incrementing = false;
}
