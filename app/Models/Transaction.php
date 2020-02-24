<?php

namespace App\Models;

use App\Models\Commission;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    public function prices()
    {
        return $this->hasMany(PropPrice::class);
    }

    public function commissions()
    {
        return $this->hasMany(Commission::class);
    }
}
