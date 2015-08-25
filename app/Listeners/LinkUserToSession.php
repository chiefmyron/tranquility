<?php namespace App\Listeners;

use App\Events\AdminUserLogin;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldBeQueued;

class LinkUserToSession {

	protected $_app;
	protected $_connection;
	protected $_table;
	
	/**
	 * Create the event handler.
	 *
	 * @return void
	 */
	public function __construct(\Illuminate\Foundation\Application $app) {
		// Get session
		$this->_app = $app; 
		$this->_connection = $this->_app['db']->connection($this->_app['config']['session.connection']);
		$this->_table = $this->_app['config']['auth.tokensTable'];
	}

	/**
	 * Handle the event.
	 *
	 * @param  AdminUserLogin  $event
	 * @return void
	 */
	public function handle(\Tranquility\Auth\User $user) {
		// Update user tokens table with session ID
		$sessionData = array(
			'sessionId' => \Auth::getSession()->getId(),
		);
		$this->getQuery()->where('userId', $user->id)->update($sessionData);
	}
	
	/**
	 * Get a fresh query builder instance for the table.
	 *
	 * @return \Illuminate\Database\Query\Builder
	 */
	protected function getQuery() {
		return $this->_connection->table($this->_table);
	}
}
