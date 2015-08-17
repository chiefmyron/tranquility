<?php namespace Tranquility\Providers;
	
use Tranquility\Auth\User;
use Tranquility\Auth\UserProvider;
use Tranquility\Services\User as UserService;
use Illuminate\Support\ServiceProvider;

class AuthProvider extends ServiceProvider {
	
	/**
	 * Bootstrap the application service
	 * @return void
	 */
	public function boot() {
		$this->app['auth']->extend('custom', function() {
			return new UserProvider($this->app['hash'], new UserService($this->app));
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