<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'attendances';
    protected $guarded = ['id'];

    public function user()
    {
        return $this->hasMany(User::class, 'user_id', 'id');
    }
}
