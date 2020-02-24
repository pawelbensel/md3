<?php

namespace App\Models;

use App\PropInactiveDate;
use Illuminate\Database\Eloquent\Model;

class Prop extends Model
{
    protected $fillable = ['source'];

    public function agents()
    {
        return $this->belongsToMany(Agent::class)->withTimestamps();
    }

    public function offices()
    {
        return $this->belongsToMany(Office::class)->withTimestamps();
    }

    public function keyValues()
    {
        return $this->morphMany(KeyValue::class, 'owner');
    }

    public function similar()
    {
        return $this->morphMany(Similar::class, 'similar');
    }

    public function addresses()
    {
        return $this->hasMany(PropAddress::class);
    }

    public function zips()
    {
        return $this->hasMany(PropZip::class);
    }

    public function lDates()
    {
        return $this->hasMany(PropLDate::class);
    }

    public function agentMlsIds()
    {
        return $this->hasMany(PropAgentMlsId::class);
    }

    public function basements()
    {
        return $this->hasMany(PropBasement::class);
    }

    public function descriptions()
    {
        return $this->hasMany(PropDescription::class);
    }

    public function garages()
    {
        return $this->hasMany(PropGarage::class);
    }

    public function mlsIds()
    {
        return $this->hasMany(PropMlsId::class);
    }

    public function mlsOfficeIds()
    {
        return $this->hasMany(PropMlsOfficeId::class);
    }

    public function mlsPrivateNumbers()
    {
        return $this->hasMany(PropMlsPrivateNumber::class);
    }

    public function onMarkets()
    {
        return $this->hasMany(PropOnMarket::class);
    }

    public function inactiveDates()
    {
        return $this->hasMany(PropInactiveDate::class);
    }

    public function pictureUrls()
    {
        return $this->hasMany(PropPictureUrl::class);
    }

    public function squareFeets()
    {
        return $this->hasMany(PropSquareFeet::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function totalRooms()
    {
        return $this->hasMany(PropTotalRoom::class);
    }

    public function totalBedRooms()
    {
        return $this->hasMany(PropTotalBedRoom::class);
    }

    public function totalDiningRooms()
    {
        return $this->hasMany(PropTotalDiningRoom::class);
    }

    public function totalEatInKitchens()
    {
        return $this->hasMany(PropTotalEatInKitchen::class);
    }

    public function totalFamilyRooms()
    {
        return $this->hasMany(PropTotalFamilyRoom::class);
    }

    public function totalLivingRooms()
    {
        return $this->hasMany(PropTotalLivingRoom::class);
    }

    public function yearBuilds()
    {
        return $this->hasMany(PropYearBuild::class);
    }
}
