<?php

namespace App\Models;

use App\OneManyModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Office extends OneManyModel
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = ['source'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function props()
    {
        return $this->belongsToMany(Prop::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function names()
    {
        return $this->hasMany('App\Models\OfficeName');
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
    public function msaIds()
    {
        return $this->hasMany('App\Models\OfficeMsaId');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function addresses()
    {
        return $this->hasMany('App\Models\OfficeAddress');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function companyNames()
    {
        return $this->hasMany('App\Models\OfficeCompanyName');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function emails()
    {
        return $this->hasMany('App\Models\OfficeEmail');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function websites()
    {
        return $this->hasMany('App\Models\OfficeWebsite');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function phones()
    {
        return $this->hasMany('App\Models\OfficePhone');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function zips()
    {
        return $this->hasMany('App\Models\OfficeZip');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function states()
    {
        return $this->hasMany('App\Models\OfficeState');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function agents()
    {
        return $this->belongsToMany('App\Models\Agent')->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function mlsIds()
    {
        return $this->hasMany('App\Models\OfficeMlsId');
    }

}
