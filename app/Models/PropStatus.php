<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropStatus extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public function prices()
    {
        return $this->hasMany(PropPrice::class);
    }
}
