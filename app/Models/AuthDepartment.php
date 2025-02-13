<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthDepartment extends Model
{
    protected $connection = 'pgsql_ngrok';
    protected $table = 'public.auth_department';
}
