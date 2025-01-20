<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersLeavePerson extends Model
{
    protected $connection = 'pgsql_ngrok';
    protected $table = 'public.pers_leaveperson';
}
