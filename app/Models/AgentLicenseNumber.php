<?php

namespace App\Models;


use App\Traits\Checkable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgentLicenseNumber extends Model
{
    use Checkable, SoftDeletes;
    protected $dates = ['deleted_at'];

    public function office()
    {
        return $this->belongsTo('App\Models\Office');
    }

}