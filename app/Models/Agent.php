<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agent extends Model
{
    use SoftDeletes;

    protected $fillable = ['source'];
    protected $dates = ['deleted_at'];

    public function firstNames()
    {
        return $this->hasMany('App\Models\AgentFirstName');
    }

    public function lastNames()
    {
        return $this->hasMany('App\Models\AgentLastName');
    }

    public function similar()
    {
        return $this->morphMany(Similar::class, 'similar');
    }

    public function emails()
    {
        return $this->hasMany('App\Models\AgentEmail');
    }

    public function mlsIds()
    {
        return $this->hasMany('App\Models\AgentMlsId');
    }

    public function licenseNumbers()
    {
        return $this->hasMany('App\Models\AgentLicenseNumber');
    }

    public function phones()
    {
        return $this->hasMany('App\Models\AgentPhone');
    }

    public function titles()
    {
        return $this->hasMany('App\Models\AgentTitle');
    }

    public function types()
    {
        return $this->hasMany('App\Models\AgentType');
    }

    public function offices()
    {
        return $this->belongsToMany('App\Models\Office')->withTimestamps();
    }

    public function props()
    {
        return $this->belongsToMany(Prop::class);
    }
}
