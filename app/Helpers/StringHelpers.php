<?php

namespace App\Helpers;
use Illuminate\Support\Str;


class StringHelpers
{
	public static function slug(?string $string) {
		if(!$string) {
		    return null;
        }
		return \Str::slug($string,'');

	}

	public static function cleanupPhoneNumber(?string $string) {
		if(!$string){
		    return null;
        }
		$phone = \Str::slug($string,'');
		if ($phone[0] == '1')
		{
			$phone = substr($phone,1);
		}
		return $phone;
	}

	public static function shortPhoneNumber(?string $string) {
		if(!$string){
		    return null;
        }
		if ((strlen($string)) > 10){
			$arr[] = substr($string,-9);
			$arr[] = substr($string,0,9);
			return $arr;
		}
		return null;
	}

	public static function escapeLike($string)
    {
        $search = array("'");
        $replace   = array("\'");
        return strtolower(str_replace($search, $replace, $string));
    }

    public static function contains(string $haystack, string $needle): bool
    {
        return strpos($haystack, $needle) !== null;
    }

}
