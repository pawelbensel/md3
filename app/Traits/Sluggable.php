<?php

namespace App\Traits;

use App\Helpers\StringHelpers;

trait Sluggable {
	
	protected $slugFields = [];

	protected static function boot()
    {        
		parent::boot();
    	$toSlug = '';
        
        foreach (self::$slugFields as $fields) {
        	$toSlug .= $fields;
        }        
        static::saving(function ($model) use ($toSlug) {
            $model->slug = StringHelpers::slug($toSlug);
        });
    }
}
