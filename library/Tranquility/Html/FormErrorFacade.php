<?php namespace Tranquility\Html;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Tranquility\Html\FormErrorBuilder
 */
class FormErrorFacade extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'form-error'; }

}