<?php

namespace App\Models;

use App\Traits\Historable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes, Historable;
    protected $dates = ['deleted_at'];

    public function prices()
    {
        return $this->hasMany(PropPrice::class);
    }

    public function commissions()
    {
        return $this->hasMany(Commission::class);
    }
}
