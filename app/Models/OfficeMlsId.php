<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfficeMlsId extends Model
{
    public function office()
    {
        return $this->belongsTo('App\Models\Office');
    }
}
