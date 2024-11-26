<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceGuest extends Model
{
    protected $table = 'attendance_guests';
    protected $guarded = ['id'];

    public function guest()
    {
        return $this->belongsTo(Guest::class, 'guest_id', 'id');
    }
}