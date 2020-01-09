<?php 

namespace App\Helpers;
use Illuminate\Support\Str;


class StringHelpers 
{
	public static function slug(string $string) {
		return Str::slug(preg_replace('/\s+/', '', $string));

	}
}