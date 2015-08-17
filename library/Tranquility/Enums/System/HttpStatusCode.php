<?php namespace Tranquility\Enums\System;

/**
 * Enumeration of entity types
 *
 * @package \Tranquility\Enum
 * @author  Andrew Patterson <patto@live.com.au>
 */

class HttpStatusCode extends \Tranquility\Enums\Enum {
	const OK = 200;
	const BadRequest = 400;
	const Unauthorized = 401;
	const Forbidden = 403;
	const NotFound = 404;
	const MethodNotAllowed = 405;
	const Conflict = 409;
	const InternalServerError = 500;
}