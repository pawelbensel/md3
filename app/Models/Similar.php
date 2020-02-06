<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Similar extends Model
{
    public function similar()
    {
        return $this->morphTo();
    }
}
