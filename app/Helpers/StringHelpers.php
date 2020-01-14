<?php 

namespace App\Helpers;
use Illuminate\Support\Str;


class StringHelpers 
{
	public static function slug(string $string) {
		
		return \Str::slug($string,'');

	}

	public static function cleanupPhoneNumber(string $string) {
		
		$phone = \Str::slug($string,'');
		if ($phone[0] == '1')
		{
			$phone = substr($phone,1);
		}
		return $phone;
	}

	public static function shortPhoneNumber(string $string) {
		
		if ((strlen($string)) > 10){
			$arr[] = substr($string,10-strlen($string));
			$arr[] = substr($string,10);
			return $arr;
		}
		return null;
	}

	public static function escapeLike($string)
    {
        $search = array("'");
        $replace   = array("\'");
        return str_replace($search, $replace, $string);
    }

}