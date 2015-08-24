<?php namespace Tranquility\Html\Toolbar;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider {

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
	public function register() {
		$this->app['toolbar'] = $this->app->share(function($app) {
			$toolbar = $this->app->make('Tranquility\Html\Toolbar\Manager');
			$toolbar->setView($app['config']['toolbar.view']);
			return $toolbar;
		});
	}
		
	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides() {
		return array('toolbar');
	}

}
