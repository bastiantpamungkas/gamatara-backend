<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $table = 'shifts';
    protected $guarded = ['id'];

    public function user()
    {
        return $this->hasOne(User::class, 'shift_id', 'id');
    }
}
