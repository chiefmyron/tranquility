<?php namespace Tranquility\Session;

use SessionHandlerInterface;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Contracts\Container\Container;
use Illuminate\Session\ExistenceAwareInterface;

class DatabaseSessionHandler implements SessionHandlerInterface, ExistenceAwareInterface {

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
     * The container instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

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
     * @param  \Illuminate\Contracts\Container\Container|null  $container
     * @return void
     */
    public function __construct(ConnectionInterface $connection, $table, Container $container = null) {
		$this->table = $table;
        $this->container = $container;
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
        $payload = $this->getDefaultPayload($data);

        if ($this->exists) {
            // Update existing session record with new data
            $this->getQuery()->where('sessionId', $sessionId)->update($payload);
        } else {
            // Create a new session record
            $payload['sessionId'] = $sessionId;
            $this->getQuery()->insert($payload);
        }
        $this->exists = true;
    } 
    
    /**
     * Get the default paylaod for the session.
     *
     * @param  string  $data
     * @return array
     */
    protected function getDefaultPayload($data) {
        $payload = array(
            'payload' => base64_encode($data),
            'lastActivity' => time()    
        );

        if (! $container = $this->container) {
            return $payload;
        }

        // If user data is available, add the user ID to the session payload
        if ($container->bound(Guard::class)) {
            $payload['userId'] = $container->make(Guard::class)->id();
        }

        // If request data is available, add IP address and user agent string to the session payload
        if ($container->bound('request')) {
            $payload['ipAddress'] = $container->make('request')->ip();

            $payload['userAgent'] = substr(
                (string) $container->make('request')->header('User-Agent'), 0, 500
            );
        }

        return $payload;
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