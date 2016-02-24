<?php namespace Tranquility\Providers;

use Session;
use Blade;
use Illuminate\Support\ServiceProvider;

class ApplicationServiceProvider extends ServiceProvider {

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
        
        // Register custom Blade functions
        Blade::directive('datetime', function($expression) {
            return "<?php echo with{$expression}->format('m/d/Y H:i'); ?>";
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