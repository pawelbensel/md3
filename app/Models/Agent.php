<?php

namespace App\Models;

use App\OneManyModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agent extends OneManyModel
{
    protected $fillable = ['source'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function offices()
    {
        return $this->belongsToMany(Office::class)->withSoftDeletes()->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function props()
    {
        return $this->belongsToMany(Prop::class)->withSoftDeletes()->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function firstNames()
    {
        return $this->hasMany('App\Models\AgentFirstName');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function lastNames()
    {
        return $this->hasMany('App\Models\AgentLastName');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function similar()
    {
        return $this->morphMany(Similar::class, 'similar','similar_type', 'similar_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function object()
    {
        return $this->morphMany(Similar::class, 'object', 'object_type', 'object_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function emails()
    {
        return $this->hasMany('App\Models\AgentEmail');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function mlsIds()
    {
        return $this->hasMany('App\Models\AgentMlsId');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function licenseNumbers()
    {
        return $this->hasMany('App\Models\AgentLicenseNumber');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function phones()
    {
        return $this->hasMany('App\Models\AgentPhone');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function titles()
    {
        return $this->hasMany('App\Models\AgentTitle');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function types()
    {
        return $this->hasMany('App\Models\AgentType');
    }
}
