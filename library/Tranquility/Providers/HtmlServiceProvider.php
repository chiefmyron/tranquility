<?php namespace Tranquility\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

use Collective\Html\FormFacade                 as Form;
use Tranquility\Html\Form\FormBuilder          as FormBuilder;
use Tranquility\Html\FormError\Builder         as FormErrorBuilder;
use Tranquility\Html\DateTimeFormatter\Builder as HtmlDateTimeFormatterBuilder;
use Tranquility\Html\Pagination\PaginationPresenter;

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
	public function register() {
		$this->registerHtmlBuilder();
		$this->registerFormBuilder();
		$this->registerFormErrorBuilder();
        $this->registerDateTimeFormatterBuilder();
        $this->registerToolbarManager();
		$this->registerActionButtonManager();
		$this->registerPaginationProvider();

		$this->app->alias('html', 'Collective\Html\HtmlBuilder');
		$this->app->alias('form', 'Tranquility\Html\Form\FormBuilder');
		$this->app->alias('form-error', 'Tranquility\Html\FormError\Builder');
        $this->app->alias('html-datetime', 'Tranquility\Html\DateTimeFormatter\Builder');
	}

	/**
	 * Bootstrap any application services
	 *
	 * @return void
	 */
	public function boot() {
		Form::component('entitySelect', 'administration._partials.form.entitySelect', ['name', 'entityType', 'value' => null, 'attributes' => []]);
	}
    
    /**
     * Register the form builder instance.
     *
     * @return void
     */
    protected function registerFormBuilder() {
        $this->app->singleton('form', function ($app) {
            $form = new FormBuilder($app['html'], $app['url'], $app['view'], $app['session.store']->getToken());
            return $form->setSessionStore($app['session.store']);
        });
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
	 * Register the date/time formatter builder instance.
	 *
	 * @return void
	 */
	protected function registerDateTimeFormatterBuilder()
	{
        $this->app->singleton('html-datetime', function ($app) {
            return new HtmlDateTimeFormatterBuilder();
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
			$toolbar->setView($app['config']['html.view-toolbar']);
			return $toolbar;
		});
	}

	/**
     * Register the action button manager
     *
     * @return void
     */
    public function registerActionButtonManager() {
		$this->app['actionButton'] = $this->app->share(function($app) {
			$actionButton = $this->app->make('Tranquility\Html\ActionButton\Manager');
			$actionButton->setView($app['config']['html.view-actionButton']);
			return $actionButton;
		});
	}

	/**
	 * Register the pagination provider
	 *
	 * @return void
	 */
	public function registerPaginationProvider() {
		Paginator::presenter(function($paginator) {
			$presenter = new PaginationPresenter($paginator);
			$presenter->setView($this->app['config']['html.view-pagination']);
            return $presenter;
        });
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides() {
		return array('html', 'form', 'form-error', 'toolbar', 'actionButton', 'html-datetime');
	}

}
