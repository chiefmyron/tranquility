<?php namespace Tranquility\Enums\BusinessObjects\Address;

/**
 * Enumeration of address types
 *
 * @package \Tranquility\Enums\BusinessObjects\Address
 * @author  Andrew Patterson <patto@live.com.au>
 */

class AddressTypes extends \Tranquility\Enums\Enum {
	const Physical = 'physical';
	const Phone = 'phone';
	const Email = 'email';
    const Social = 'social';
    const Web = 'web';
}