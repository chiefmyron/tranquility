<?php namespace Tranquility\Providers;
	
use \Auth;
use Tranquility\Auth\User;
use Tranquility\Providers\UserServiceProvider;
use Tranquility\Services\UserService as UserService;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider {
	
	/**
	 * Bootstrap the application service
	 * @return void
	 */
	public function boot() {
        Auth::provider('custom', function($app, array $config) {
            return new UserServiceProvider($this->app['hash'], new UserService($this->app['em']));
        });
	}
	
	/**
	 * Register the application service
	 * @return void
	 */
	public function register() {
		//
	}
}