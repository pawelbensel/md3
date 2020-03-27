<?php

namespace App\Models;

use App\OneManyModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prop extends OneManyModel
{
    protected $fillable = ['source'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function agents()
    {
        return $this->belongsToMany(Agent::class)->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function offices()
    {
        return $this->belongsToMany(Office::class)->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function keyValues()
    {
        return $this->morphMany(KeyValue::class, 'owner');
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
    public function addresses()
    {
        return $this->hasMany(PropAddress::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function zips()
    {
        return $this->hasMany(PropZip::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function lDates()
    {
        return $this->hasMany(PropLDate::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function agentMlsIds()
    {
        return $this->hasMany(PropAgentMlsId::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function basements()
    {
        return $this->hasMany(PropBasement::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function descriptions()
    {
        return $this->hasMany(PropDescription::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function garages()
    {
        return $this->hasMany(PropGarage::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function mlsIds()
    {
        return $this->hasMany(PropMlsId::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function mlsOfficeIds()
    {
        return $this->hasMany(PropMlsOfficeId::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function mlsPrivateNumbers()
    {
        return $this->hasMany(PropMlsPrivateNumber::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function onMarkets()
    {
        return $this->hasMany(PropOnMarket::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inactiveDates()
    {
        return $this->hasMany(PropInactiveDate::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pictureUrls()
    {
        return $this->hasMany(PropPictureUrl::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function squareFeets()
    {
        return $this->hasMany(PropSquareFeet::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function totalRooms()
    {
        return $this->hasMany(PropTotalRoom::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function totalBedRooms()
    {
        return $this->hasMany(PropTotalBedRoom::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function totalDiningRooms()
    {
        return $this->hasMany(PropTotalDiningRoom::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function totalEatInKitchens()
    {
        return $this->hasMany(PropTotalEatInKitchen::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function totalFamilyRooms()
    {
        return $this->hasMany(PropTotalFamilyRoom::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function totalLivingRooms()
    {
        return $this->hasMany(PropTotalLivingRoom::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function yearBuilds()
    {
        return $this->hasMany(PropYearBuild::class);
    }
}
