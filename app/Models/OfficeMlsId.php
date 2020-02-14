<?php

namespace App\Models;

use App\Traits\Checkable;
use App\Traits\Historable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfficeMlsId extends Model
{
    use Checkable, SoftDeletes, Historable;
    protected $dates = ['deleted_at'];

    public function office()
    {
        return $this->belongsTo('App\Models\Office');
    }
}
