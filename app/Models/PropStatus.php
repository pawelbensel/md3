<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropStatus extends Model
{
    public function prices()
    {
        return $this->hasMany(PropPrice::class);
    }
}
