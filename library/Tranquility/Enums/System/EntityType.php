<?php namespace Tranquility\Enums\System;

/**
 * Enumeration of entity types
 *
 * @package \Tranquility\Enum
 * @author  Andrew Patterson <patto@live.com.au>
 */

class EntityType extends \Tranquility\Enums\Enum {
	const Person  = 'person';
	const Address = 'address';
	const Content = 'content';
	const User    = 'user';
    const AddressPhysical = 'addressPhysical';
    const AddressPhone = 'addressPhone';
    const AddressElectronic = 'addressElectronic';
}