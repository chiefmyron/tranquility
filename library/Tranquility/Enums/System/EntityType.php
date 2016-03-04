<?php namespace Tranquility\Enums\System;

/**
 * Enumeration of entity types
 *
 * @package \Tranquility\Enum
 * @author  Andrew Patterson <patto@live.com.au>
 */

class EntityType extends \Tranquility\Enums\Enum {
	const Person  = 'person';
	const Content = 'content';
	const User    = 'user';
    const Address = 'address';
    const AddressPhysical = 'addressPhysical';
}