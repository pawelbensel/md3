<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Office extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = ['source'];

    public function props()
    {
        return $this->belongsToMany(Prop::class);
    }

    public function names()
    {
        return $this->hasMany('App\Models\OfficeName');
    }

    public function similar()
    {
        return $this->morphMany(Similar::class, 'similar');
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

    public function emails()
    {
        return $this->hasMany('App\Models\OfficeEmail');
    }

    public function websites()
    {
        return $this->hasMany('App\Models\OfficeWebsite');
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
        return $this->belongsToMany('App\Models\Agent')->withTimestamps();
    }

    public function mlsIds()
    {
        return $this->hasMany('App\Models\OfficeMlsId');
    }
}
