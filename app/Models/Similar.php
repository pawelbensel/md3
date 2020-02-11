<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Similar extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public function similar()
    {
        return $this->morphTo();
    }
}
