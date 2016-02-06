<?php namespace Tranquility\Html\DateTimeFormatter;

use Illuminate\Support\Traits\Macroable;

use Carbon\Carbon;
use \Session;
use Tranquility\Utility;
use Tranquility\Enums\System\DateTimeFormatTypes as EnumFormatStrings;

class Builder {

	use Macroable;
	
	/**
	 * Display an inline error message as a span
	 */
	public function format($dateTime, $formatString = null, $options = array(), $localeCode = null, $timezoneCode = null) {
        // If datetime is provided as a string, convert into Carbon object
        if (is_string($dateTime)) {
            $dateTime = new Carbon($dateTime, Config::get('app.timezone', 'UTC'));
        }
        if ($dateTime instanceof \DateTime) {
            $dateTime = Carbon::instance($dateTime);
        }
        
        // Check for custom format strings
        if (EnumFormatStrings::isValidLabel($formatString)) {
            $classname = EnumFormatStrings::class;
            $formatString = constant($classname."::$formatString");
        }
        
        // Get user locale and timezone from session
        if (is_null($localeCode)) {
            $localeCode = Session::get('tranquility.localeFormatCode');
        }
        if (is_null($timezoneCode)) {
            $timezoneCode = Session::get('tranquility.timezoneFormatCode');
        }
        
        // Format object and return string
        $html = '<time datetime="'.$dateTime->toRfc3339String().'">'.$dateTime->setTimezone($timezoneCode)->format($formatString).'</time>';
        return $html;        
	}
    
    public function shortDate($dateTime, $localeCode = null, $timezoneCode = null) {
        return $this->format($dateTime, 'ShortDate', $localeCode, $timezoneCode);
    }
    
    public function longDate($dateTime, $localeCode = null, $timezoneCode = null) {
        return $this->format($dateTime, 'LongDate', $localeCode, $timezoneCode);
    }
    
    public function shortDateTime($dateTime, $militaryTime = false, $localeCode = null, $timezoneCode = null) {
        if ($militaryTime) {
            $formatString = 'ShortDateTime24Hour';
        } else {
            $formatString = 'ShortDateTime12Hour';
        }
        return $this->format($dateTime, $formatString, $localeCode, $timezoneCode);
    }
    
    public function longDateTime($dateTime, $militaryTime = false, $localeCode = null, $timezoneCode = null) {
        if ($militaryTime) {
            $formatString = 'LongDateTime24Hour';
        } else {
            $formatString = 'LongDateTime12Hour';
        }
        return $this->format($dateTime, $formatString, $localeCode, $timezoneCode);
    }
    
    public function time($dateTime, $militaryTime = false, $localeCode = null, $timezoneCode = null) {
        if ($militaryTime) {
            $formatString = 'Time24Hour';
        } else {
            $formatString = 'Time12Hour';
        }
        return $this->format($dateTime, $formatString, $localeCode, $timezoneCode);
    }
}
