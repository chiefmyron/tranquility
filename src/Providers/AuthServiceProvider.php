<?php namespace Tranquility\Providers;

use Auth;
use Tranquility\Auth\User;
use Tranquility\Providers\UserServiceProvider;
use Tranquility\Services\UserService as UserService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider {
    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Register custom Guard for authentication
        Auth::provider('custom', function($app, array $config) {
            return new UserServiceProvider($this->app['hash'], new UserService($this->app['em']));
        });
    }
}
