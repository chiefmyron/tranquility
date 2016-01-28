<?php namespace Tranquility\Providers;

use Illuminate\Contracts\Hashing\Hasher         as HasherContract;	
use Illuminate\Contracts\Auth\UserProvider      as UserProviderInterface;
use Illuminate\Contracts\Auth\Authenticatable   as Authenticatable;

use Tranquility\Data\BusinessObjects\User       as User;
use Tranquility\Utility                         as Utility;
use Tranquility\Services\User                   as UserService;

class UserServiceProvider implements UserProviderInterface {		
	
	/**
	 * The data service used to retrieve user details
	 *
	 * @var \Tranquility\Services\User
	 */
	protected $_service;
	
	/**
	 * The hasher implementation.
	 *
	 * @var \Illuminate\Contracts\Hashing\Hasher
	 */
	protected $_hasher;
	
	public function __construct(HasherContract $hasher, UserService $service) {
		$this->_hasher = $hasher;
		$this->_service = $service;
	}
	
	/**
	 * Retrieve a user by their unique identifier.
	 *
	 * @param  mixed  $identifier
	 * @return \Illuminate\Contracts\Auth\Authenticatable|null
	 */
	public function retrieveById($identifier) {
		// Retrieve user record
		$response = $this->_service->find($identifier);
		if ($response->containsErrors()) {
			return null;
		}
		
		// Create user object and return
		$user = $response->getFirstContentItem();
		return $user;
	}

	/**
	 * Retrieve a user by by their unique identifier and "remember me" token.
	 *
	 * @param  mixed   $identifier
	 * @param  string  $token
	 * @return \Illuminate\Contracts\Auth\Authenticatable|null
	 */
	public function retrieveByToken($identifier, $token) {
		// Retrieve user record
		$response = $this->_service->findByToken($identifier, $token);
		if ($response->containsErrors()) {
			return null;
		}
		
		// Create user object and return
        $user = $response->getFirstContentItem();
		return $user;
	}

	/**
	 * Update the "remember me" token for the given user in storage.
	 *
	 * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
	 * @param  string  $token
	 * @return void
	 */
	public function updateRememberToken(Authenticatable $user, $token) {
		$this->_service->updateRememberToken($user->id, $token);
	}

	/**
	 * Retrieve a user by the given credentials (email and password)
	 *
	 * @param  array  $credentials
	 * @return \Illuminate\Contracts\Auth\Authenticatable|null
	 */
	public function retrieveByCredentials(array $credentials) {
		// Retrieve user record
		$username = Utility::extractValue($credentials, 'email', '');
		$response = $this->_service->findBy('username', $username);
		if ($response->containsErrors()) {
			return null;
		}
        
		// Create user object and return
		$user = $response->getFirstContentItem();
		return $user;
	}

	/**
	 * Validate a user against the given credentials.
	 *
	 * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
	 * @param  array  $credentials
	 * @return bool
	 */
	public function validateCredentials(Authenticatable $user, array $credentials) {
		$plain = Utility::extractValue($credentials, 'password', '');
		return $this->_hasher->check($plain, $user->getAuthPassword());
	}
}