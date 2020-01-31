<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KeyValue extends Model
{
    public function owner()
    {
        return $this->morphTo();
    }
}
