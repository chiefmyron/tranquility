<?php namespace App\Http\Controllers\Administration;

use \Event as Event;
use \Session as Session;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;

use App\Events\AdminUserLogin;
use App\Http\Controllers\Controller;	

use Tranquility\Enums\System\MessageLevel as EnumMessageLevel;

/*
|--------------------------------------------------------------------------
| Registration & Login Controller
|--------------------------------------------------------------------------
|
| This controller handles the registration of new users, as well as the
| authentication of existing users. By default, this controller uses
| a simple trait to add these behaviors. Why don't you explore it?
|
*/
class AuthController extends Controller {
	
	/**
	 * The Guard implementation.
	 *
	 * @var \Illuminate\Contracts\Auth\Guard
	 */
	protected $auth;

	/**
	 * Create a new authentication controller instance.
	 *
	 * @param  \Illuminate\Contracts\Auth\Guard  $auth
	 * @return void
	 */
	public function __construct(Guard $auth) {
		$this->auth = $auth;
	}

	/**
	 * Show the administration section login form.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		return view('administration.auth.login');
	}

	/**
	 * Handle a login request to the application.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function login(Request $request) {
		$messages = array();
				
		// Validate form inputs
		if (!$request->has('email')) {
			$messages[] = array(
				'code' => 10002,
				'text' => 'message_10002_mandatory_input_field_missing',
				'fieldId' => 'email',
				'level' => EnumMessageLevel::Error
			);
		}
		if (!$request->has('password')) {
			$messages[] = array(
				'code' => 10002,
				'text' => 'message_10002_mandatory_input_field_missing',
				'fieldId' => 'password',
				'level' => EnumMessageLevel::Error
			);
		}
		$validator = \Validator::make($request->all(), ['email' => 'email']);
		if ($validator->fails()) {
			$messages[] = array(
				'code' => 10004,
				'text' => 'message_10004_username_must_be_email_address',
				'fieldId' => 'email',
				'level' => EnumMessageLevel::Error
			);
		}
		
		// If validation failed, return with errors
		if (count($messages) > 0) {
			Session::flash('messages', $messages);
			return redirect($this->loginPath())->withInput($request->only('email', 'remember'));
		}
		
		// Extract credentials from request
		$credentials = $request->only('email', 'password');

		// Attempt authentication
		if ($this->auth->attempt($credentials, $request->has('remember'))) {
			// Successful authentication - redirect to intended destination
			return redirect()->intended($this->redirectPath());
		}

		// Authentication failed - return to login screen
		$messages[] = array(
			'code' => 10006,
			'text' => 'message_10006_invalid_login_credentials',
			'level' => EnumMessageLevel::Error		
		);
		Session::flash('messages', $messages);
		return redirect($this->loginPath())->withInput($request->only('email', 'remember'));
	}

	/**
	 * Log the user out of the application.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function logout() {
		$this->auth->logout();
		return redirect(property_exists($this, 'redirectAfterLogout') ? $this->redirectAfterLogout : action('WelcomeController@index'));
	}

	/**
	 * Get the post register / login redirect path.
	 *
	 * @return string
	 */
	public function redirectPath() {
		if (property_exists($this, 'redirectPath')) {
			return $this->redirectPath;
		}

		return property_exists($this, 'redirectTo') ? $this->redirectTo : action('Administration\HomeController@index');
	}

	/**
	 * Get the path to the login route.
	 *
	 * @return string
	 */
	public function loginPath()
	{
		return property_exists($this, 'loginPath') ? $this->loginPath : action('Administration\AuthController@index');
	}
	
	/**
	 * Display the form to request a password reset link.
	 *
	 * @return Response
	 */
	public function forgotPassword() {
		return view('administration.auth.forgotPassword');
	}
}