<?php namespace Tranquility\Providers;

use View;
use Illuminate\Support\ServiceProvider;

class ViewComposerProvider extends ServiceProvider {

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function boot() {
        // Using class based composers...
        View::composer('*', 'Tranquility\ViewComposers\CurrentUserViewComposer');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        //
    }

}