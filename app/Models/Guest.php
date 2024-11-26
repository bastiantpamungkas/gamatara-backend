<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    protected $table = 'guests';
    protected $guarded = ['id'];

    public function attendance_guest()
    {
        return $this->hasMany(AttendanceGuest::class, 'guest_id', 'id');
    }
}
