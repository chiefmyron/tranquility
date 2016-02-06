<?php namespace Tranquility\Enums\System;

/**
 * Enumeration of entity types
 *
 * @package \Tranquility\Enum
 * @author  Andrew Patterson <patto@live.com.au>
 */

class DateTimeFormatTypes extends \Tranquility\Enums\Enum {
	const ShortDate           = 'd/m/Y';
	const LongDate            = 'jS F, Y';
	const ShortDateTime12Hour = 'g:ia T, d/m/Y';
    const ShortDateTime24Hour = 'd/m/Y H:i';
	const LongDateTime12Hour  = 'g:ia T, jS F Y';
    const LongDateTime24Hour  = 'H:i T, jS F Y';
    const Time12Hour          = 'g:ia T';
    const Time24Hour          = 'H:i T';
}