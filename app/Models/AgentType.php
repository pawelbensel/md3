<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class AgentType extends Model
{
    use HasSlug;

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('type')
            ->saveSlugsTo('slug')
            ->usingSeparator('')
            ->allowDuplicateSlugs();
    }
}
