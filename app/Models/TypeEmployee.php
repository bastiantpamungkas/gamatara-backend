<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeEmployee extends Model
{
    protected $table = 'type_employees';
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'type_employee_id');
    }
}
