<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttLog extends Model
{
    protected $table = 'att_logs';
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
