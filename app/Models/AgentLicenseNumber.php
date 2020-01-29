<?php

namespace App\Models;


use App\Traits\Checkable;
use Illuminate\Database\Eloquent\Model;

class AgentLicenseNumber extends Model
{
    use Checkable;

    public function office()
    {
        return $this->belongsTo('App\Models\Office');
    }

}