<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'companies';
    protected $guarded = ['id'];

    public function user()
    {
        return $this->hasOne(User::class, 'company_id', 'id');
    }
}
