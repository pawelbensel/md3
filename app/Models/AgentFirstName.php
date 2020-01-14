<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class AgentFirstName extends Model
{
    use HasSlug;

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('first_name')
            ->saveSlugsTo('slug')
            ->usingSeparator('')
            ->allowDuplicateSlugs();
    }


}
