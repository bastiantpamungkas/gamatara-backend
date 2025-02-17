<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBatik extends Model
{
    protected $connection = 'mysql';
    protected $table = 'users';
    protected $fillable = ['id', 'name', 'email', 'pin', 'shift_id', 'type_employee_id', 'company_id', 'status', 'shift_id2', 'department'];

    public $timestamps = false;
    public $incrementing = false;
}
