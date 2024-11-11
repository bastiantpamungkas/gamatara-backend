<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    protected $table = 'guests';
    protected $guarded = ['id'];

    public function attendance_guest()
    {
        return $this->belongsTo(AttendanceGuest::class, 'id', 'guest_id');
    }
}
