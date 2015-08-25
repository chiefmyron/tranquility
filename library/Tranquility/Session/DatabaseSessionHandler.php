<?php namespace Tranquility\Session;

use \SessionHandlerInterface;
use \Illuminate\Database\ConnectionInterface;

class DatabaseSessionHandler implements SessionHandlerInterface, \Illuminate\Session\ExistenceAwareInterface {

	/**
	 * The database connection instance.
	 *
	 * @var \Illuminate\Database\ConnectionInterface
	 */
	protected $connection;

	/**
	 * The name of the session table.
	 *
	 * @var string
	 */
	protected $table;

	/**
	 * The existence state of the session.
	 *
	 * @var bool
	 */
	protected $exists;

	/**
	 * Create a new database session handler instance.
	 *
	 * @param  \Illuminate\Database\ConnectionInterface  $connection
	 * @param  string  $table
	 * @return void
	 */
	public function __construct(ConnectionInterface $connection, $table) {
		$this->table = $table;
		$this->connection = $connection;
	}

	/**
	 * Open file for storing session data (not required)
	 */
	public function open($savePath, $sessionName) {
		return true;
	}

	/**
	 * Close file for storing session data (not required)
	 */
	public function close() {
		return true;
	}

	/**
	 * Return the string version of the session data associated with 
	 * the given $sessionId.
	 *
	 * @var $sessionId  string  Identifier for the session 
	 * @return string
	 */
	public function read($sessionId) {
		$session = (object) $this->getQuery()->where('sessionId', $sessionId)->first();
		if (isset($session->payload)) {
			$this->exists = true;
			return base64_decode($session->payload);
		}
	}

	/**
	 * Create a new session with data, or update an existing session 
	 * with new information
	 *
	 * @var $sessionId  string  Identifier for the session
	 * @var $data       string  Session data
	 */
	public function write($sessionId, $data) {
		if ($this->exists) {
			// Update existing session record with new data
			$sessionData = array(
				'payload' => base64_encode($data),
				'lastActivity' => time()
			);
			$this->getQuery()->where('sessionId', $sessionId)->update($sessionData);
		} else {
			// Create a new session record
			$sessionData = array(
				'sessionId' => $sessionId,
				'payload' => base64_encode($data),
				'lastActivity' => time()
			);
			$this->getQuery()->insert($sessionData);
		}

		$this->exists = true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function destroy($sessionId) {
		$this->getQuery()->where('sessionId', $sessionId)->delete();
	}

	/**
	 * {@inheritDoc}
	 */
	public function gc($lifetime) {
		$this->getQuery()->where('lastActivity', '<=', time() - $lifetime)->delete();
	}

	/**
	 * Get a fresh query builder instance for the table.
	 *
	 * @return \Illuminate\Database\Query\Builder
	 */
	protected function getQuery() {
		return $this->connection->table($this->table);
	}

	/**
	 * Set the existence state for the session.
	 *
	 * @param  bool  $value
	 * @return $this
	 */
	public function setExists($value) {
		$this->exists = $value;
		return $this;
	}
}