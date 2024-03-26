<?php


class Excerpt
{
	public function __construct()
	{

	}
	public static function getExcerpt($str, $length)
	{
		if(strlen($str) >= $length)
		{
			return substr($str, 0, $length - 3) . "...";
		}
		return $str;
	}

}