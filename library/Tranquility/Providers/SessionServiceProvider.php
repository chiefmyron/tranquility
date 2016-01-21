<?php namespace Tranquility\Providers;

use Session;
use Illuminate\Support\ServiceProvider;

class SessionServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot() {
		// Register custom session handler
		Session::extend('tranquility', function($app) {
			// Get database configuration
			$connection = $this->app['db']->connection($this->app['config']['session.connection']);
			$table = $this->app['config']['session.table'];
			
			return new \Tranquility\Session\DatabaseSessionHandler($connection, $table, $this->app);
		});
	}

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

}