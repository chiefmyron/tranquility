<?php namespace Tranquility\Providers;

use Illuminate\Support\ServiceProvider;
use Tranquility\Html\FormError\Builder as FormErrorBuilder;

class HtmlServiceProvider extends \Collective\Html\HtmlServiceProvider {

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
        $this->registerToolbarManager();

		$this->app->alias('html', 'Collective\Html\HtmlBuilder');
		$this->app->alias('form', 'Collective\Html\FormBuilder');
		$this->app->alias('form-error', 'Tranquility\Html\FormError\Builder');
	}
    
	/**
	 * Register the form error builder instance.
	 *
	 * @return void
	 */
	protected function registerFormErrorBuilder()
	{
        $this->app->singleton('form-error', function ($app) {
            return new FormErrorBuilder($app['session.store']->getToken());
        });
	}
    
    /**
     * Register the toolbar manager
     *
     * @return void
     */
    public function registerToolbarManager() {
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
	public function provides()
	{
		return array('html', 'form', 'form-error', 'toolbar');
	}

}
