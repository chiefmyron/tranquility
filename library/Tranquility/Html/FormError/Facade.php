<?php namespace Tranquility\Html\FormError;

use Illuminate\Support\Facades\Facade as BaseFacade;

/**
 * @see \Tranquility\Html\FormError\Builder
 */
class Facade extends BaseFacade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'form-error'; }

}