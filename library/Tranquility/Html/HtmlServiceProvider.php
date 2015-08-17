<?php namespace Tranquility\Html;

use Illuminate\Support\ServiceProvider;

class HtmlServiceProvider extends \Illuminate\Html\HtmlServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = true;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerHtmlBuilder();
		$this->registerFormBuilder();
		$this->registerFormErrorBuilder();

		$this->app->alias('html', 'Illuminate\Html\HtmlBuilder');
		$this->app->alias('form', 'Illuminate\Html\FormBuilder');
		$this->app->alias('form-error', 'Tranquility\Html\FormErrorBuilder');
	}

	/**
	 * Register the form error builder instance.
	 *
	 * @return void
	 */
	protected function registerFormErrorBuilder()
	{
		$this->app->bindShared('form-error', function($app)
		{
			return new FormErrorBuilder($app['session.store']->getToken());
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('html', 'form', 'form-error');
	}

}
