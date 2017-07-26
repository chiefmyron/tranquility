<?php namespace Tranquility\Enums\System;

/**
 * Enumeration of message levels
 *
 * @package \Tranquility\Enum
 * @author  Andrew Patterson <patto@live.com.au>
 */

class MessageLevel extends \Tranquility\Enums\Enum {
	const Error   = 'error';
	const Warning = 'warning';
	const Info    = 'info';
	const Success = 'success';
}