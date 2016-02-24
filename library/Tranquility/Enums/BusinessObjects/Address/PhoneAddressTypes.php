<?php namespace Tranquility\Enums\BusinessObjects\Address;

/**
 * Enumeration of phone address types
 *
 * @package \Tranquility\Enum
 * @author  Andrew Patterson <patto@live.com.au>
 */

class PhoneAddressTypes extends \Tranquility\Enums\Enum {
	const Mobile = 'mobile';
	const Home = 'home';
	const Work = 'work';
	const Company = 'company';
    const Pager = 'pager';
    const Fax = 'fax';
}