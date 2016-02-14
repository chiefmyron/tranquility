<?php namespace Tranquility\Enums\BusinessObjects\Address;

/**
 * Enumeration of physical address types
 *
 * @package \Tranquility\Enum
 * @author  Andrew Patterson <patto@live.com.au>
 */

class PhysicalAddresstypes extends \Tranquility\Enums\Enum {
	const Home = 'home';
	const Work = 'work';
	const Billing = 'billing';
	const Delivery = 'delivery';
}