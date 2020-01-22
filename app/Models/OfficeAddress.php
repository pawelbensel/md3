<?php

namespace App\Models;

use App\Traits\Checkable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class OfficeAddress extends Model
{
	use HasSlug, Checkable;

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(['address1','address2','city'])
            ->saveSlugsTo('slug')
            ->usingSeparator('')
            ->allowDuplicateSlugs();
    }    
}
