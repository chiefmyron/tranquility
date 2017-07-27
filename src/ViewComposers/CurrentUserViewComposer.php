<?php namespace Tranquility\ViewComposers;

use Illuminate\Contracts\View\View;
use Illuminate\Contracts\Auth\Guard;

class CurrentUserViewComposer {

    /**
     * The authentication mechanism used to identify the current user
     *
     * @var Guard
     */
    protected $_auth;

    /**
     * Create a new current user composer.
     *
     * @param  Guard  $auth
     * @return void
     */
    public function __construct(Guard $auth) {
        // Dependencies automatically resolved by service container...
        $this->_auth = $auth;
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view) {
        $view->with('_currentUser', $this->_auth->user());
    }

}