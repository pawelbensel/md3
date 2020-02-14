<?php

namespace App\Models;

use App\Traits\Checkable;
use App\Traits\Historable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class OfficeAddress extends Model
{
	use HasSlug, Checkable, SoftDeletes, Historable;
    protected $dates = ['deleted_at'];

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(['address1','address2','city'])
            ->saveSlugsTo('slug')
            ->usingSeparator('')
            ->allowDuplicateSlugs();
    }    
}
