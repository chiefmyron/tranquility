<?php namespace Tranquility\Providers;

use Illuminate\Pagination\PaginationServiceProvider as LaravelPaginationServiceProvider;

class PaginationServiceProvider extends LaravelPaginationServiceProvider {
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {
        $this->loadViewsFrom($this->app['config']['html.view-pagination'], 'pagination');
        parent::boot();
    }
}
