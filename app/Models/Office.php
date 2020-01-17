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
 
    public function addresses()
    {
        return $this->hasMany('App\Models\OfficeAddress');
    }
    public function companyNames()
    {
        return $this->hasMany('App\Models\OfficeCompanyName');
    }

    public function phones()
    {
        return $this->hasMany('App\Models\OfficePhone');
    }

    public function zips()
    {
        return $this->hasMany('App\Models\OfficeZip');
    }

    public function states()
    {
        return $this->hasMany('App\Models\OfficeState');
    }

    public function agents()
    {
        return $this->belongsToMany('App\Models\Agent');
    }

    public function mlsIds()
    {
        return $this->hasMany('App\Models\OfficeMlsId');
    }
}
