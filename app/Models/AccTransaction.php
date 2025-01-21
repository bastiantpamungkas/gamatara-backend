<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccTransaction extends Model
{
    protected $connection = 'pgsql_ngrok';
    protected $table = 'public.acc_transaction';

    public function pers_person()
    {
        return $this->belongsTo(PersPerson::class, 'pin', 'pin');
    }
}
