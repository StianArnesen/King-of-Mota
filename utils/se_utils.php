<?php

class StaticUtils
{
	public static function currencyFormat($n){
		$number = 1234.56;
        $english_format_number = number_format($number);

        return number_format($n, 0, '.', ',');
	}
	public static function getTimeFormatted($_timestamp)
	{
		if(isset($_timestamp) && is_numeric($_timestamp))
		{
			$_SEC 		= $_timestamp % 60;
			$_MIN 		= floor(($_timestamp / 60) % 60);
			$_HRS 		= floor(($_timestamp / 3600));
			$_DAYS 		= floor($_timestamp / (3600*24));
			$_MONTHS 	= floor($_timestamp / (3600*24*31));
			$_YEARS 	= floor($_timestamp / (3600*24*31*12));

			$result = array(
				"seconds" 	=> $_SEC,
			 	"minutes" 	=> $_MIN,
			 	"hours" 	=> $_HRS,
			 	"days" 		=> $_DAYS,
			 	"months" 	=> $_MONTHS,
			 	"years" 	=> $_YEARS
				);

			return $result;
		}
		return null;
	}
    public static function getExcerpt($str, $length)
    {
        if(strlen($str) >= $length)
        {
            return substr($str, 0, $length - 3) . "...";
        }
        return $str;
    }

    public static function getFormattedTimeToCleanText($formatted_time)
	{
		if($formatted_time['years'] >= 1)
		{
			if($formatted_time['years'] > 1)
			{
				return $formatted_time['years'] . " years";
			}
			return $formatted_time['years'] . " year";
		}
		else if($formatted_time['months'] >= 1)
		{
			if($formatted_time['months'] > 1)
			{
				return $formatted_time['months'] . " months";
			}
			return $formatted_time['months'] . " month";
		}
		else if($formatted_time['days'] >= 1)
		{
			if($formatted_time['days'] > 1)
			{
				return $formatted_time['days'] . " days";
			}
			return $formatted_time['days'] . " day";
		}
		else if($formatted_time['hours'] >= 1)
		{
			if($formatted_time['hours'] > 1)
			{
				return $formatted_time['hours'] . " hours";
			}
			return $formatted_time['hours'] . " hour";
		}
		else if($formatted_time['minutes'] >= 1)
		{
			if($formatted_time['minutes'] > 1)
			{
				return $formatted_time['minutes'] . " minutes";
			}
		}
        return "less than one minute";
	}

    /*|___________________________________________________________________________ |
     *| function time_elapsed_string();                                            |
     *|----------------------------------------------------------------------------|
     *| Author / source : http://stackoverflow.com/users/67332/glavi%C4%87         |
     *|----------------------------------------------------------------------------|
     *|                                                                            |
     *| Example usage:                                                             |
     *|  1. echo time_elapsed_string('2013-05-01 00:22:35');                       |
     *|  2. echo time_elapsed_string('@1367367755'); # timestamp input             |
     *|  3. echo time_elapsed_string('2013-05-01 00:22:35', true);                 |
     *|----------------------------------------------------------------------------|
     * */
    public static function time_elapsed_string($datetime, $full = false) {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }
}