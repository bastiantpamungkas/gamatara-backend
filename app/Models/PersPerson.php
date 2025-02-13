<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersPerson extends Model
{
    protected $connection = 'pgsql_ngrok';
    protected $table = 'public.pers_person';

    public function department()
    {
        return $this->belongsTo(AuthDepartment::class, 'auth_dept_id', 'id');
    }
}
