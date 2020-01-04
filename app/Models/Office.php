<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    protected $fillable = ['source'];

    public function names()
    {
        return $this->hasMany('App\Models\OfficeName');
    }

    public function msaIds()
    {
        return $this->hasMany('App\Models\OfficeMsaId');
    }
}
