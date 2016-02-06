<?php namespace App\Listeners;

use \Config;
use \Session;
use Illuminate\Auth\Events\Login as LoginEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldBeQueued;

use Tranquility\Utility;
use Tranquility\Data\BusinessObjects\UserBusinessObject as User;

class SetUserTimezoneAndLocale {

	protected $_app;
	
	/**
	 * Create the event handler.
	 *
	 * @return void
	 */
	public function __construct(\Illuminate\Foundation\Application $app) {
		// Get application container
		$this->_app = $app; 
	}

	/**
	 * Handle the event.
	 *
	 * @param  Illuminate\Auth\Events\Login  $event
	 * @return void
	 */
	public function handle(LoginEvent $event) {
        // Get application default timezone and locale settings
        $defaultLocale = Config::get('app.locale');
        $defaultTimezone = Config::get('app.timezone');
        
        $user = $event->user;
        $userLocale = Utility::extractValue($user, 'localeCode', $defaultLocale);
        $userTimezone = Utility::extractValue($user, 'timezoneCode', $defaultTimezone);
        
        // Set formats in session
        Session::put('tranquility.localeFormatCode', $userLocale);
        Session::put('tranquility.timezoneFormatCode', $userTimezone);
	}
}
