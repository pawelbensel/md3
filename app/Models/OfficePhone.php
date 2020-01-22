<?php

namespace App\Models;

use App\Traits\Checkable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;


class OfficePhone extends Model
{  

    use HasSlug, Checkable;

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('phone')
            ->saveSlugsTo('slug')
            ->usingSeparator('')
            ->allowDuplicateSlugs();
    }
}
