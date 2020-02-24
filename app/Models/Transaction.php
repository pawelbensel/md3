<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    public function prices()
    {
        return $this->hasMany(PropPrice::class);
    }
}
