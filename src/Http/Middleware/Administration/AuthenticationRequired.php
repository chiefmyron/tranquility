<?php namespace App\Http\Middleware\Administration;

use Closure;
use \Session as Session;
use Illuminate\Contracts\Auth\Guard;

use Tranquility\Enums\System\MessageLevel as EnumMessageLevel;

class AuthenticationRequired {

	/**
	 * The Guard implementation.
	 *
	 * @var Guard
	 */
	protected $auth;

	/**
	 * Create a new filter instance.
	 *
	 * @param  Guard  $auth
	 * @return void
	 */
	public function __construct(Guard $auth) {
		$this->auth = $auth;
	}

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)	{
		if ($this->auth->guest()) {
			if ($request->ajax()) {
				return response('Unauthorized.', 401);
			} else {
				// TODO: Set up flash message
				$messages = array();
				$messages[] = array(
					'code' => 10007,
					'text' => 'message_10007_must_be_logged_in_to_access',
					'level' => EnumMessageLevel::Info
				);
				Session::flash('messages', $messages);
				return redirect()->guest('administration/auth');
			}
		}

		return $next($request);
	}
}
