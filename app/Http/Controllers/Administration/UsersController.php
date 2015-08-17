<?php namespace App\Http\Controllers\Administration;

use \Session as Session;
use Illuminate\Http\Request as Request;
use App\Http\Controllers\Controller;

use Carbon\Carbon;
use Tranquility\Services\User as UserService;
use Tranquility\Enums\System\EntityType as EnumEntityType;
use Tranquility\Enums\System\TransactionSource as EnumTransactionSource;

class UsersController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Users controller
	|--------------------------------------------------------------------------
	|
	| This controller renders the "marketing page" for the application and
	| is configured to only allow guests. Like most of the other sample
	| controllers, you are free to modify or remove it as you desire.
	|
	*/
	
	// Users service
	private $_service;

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(UserService $service)
	{
		$this->_service = $service;
		//$this->middleware('guest');
	}

	/**
	 * Show the application welcome screen to the user.
	 *
	 * @return Response
	 */
	public function index()
	{
		$response = $this->_service->all();
		return view('administration.users.index', $response->toArray());
	}
	
	public function show($id) {
		$response = $this->_service->find($id);
		if ($response->containsErrors()) {
			// Redirect to index with error message
			
		}
		return view('administration.users.show')->with('user', $response->getFirstContentItem());
	}
	
	public function create() {
		return view('administration.users.create');
	}
	
	public function update($id) {
		$response = $this->_service->find($id);
		if ($response->containsErrors()) {
			// Redirect to index with error message
		}
		return view('administration.users.update')->with('user', $response->getFirstContentItem());
	}
	
	public function store(Request $request) {
		// Save details of person
		$params = $request->all();
		
		// Add in additional audit trail details
		$params['parentId'] = 1;
		$params['type'] = EnumEntityType::User;
		$params['updateBy'] = 1;
		$params['updateReason'] = 'who knows?';
		$params['updateDatetime'] = Carbon::now();
		$params['transactionSource'] = EnumTransactionSource::UIBackend;
		
		// Create or update record		
		if ($request->has('id')) {
			$result = $this->_service->update($request->id, $params);
		} else {
			$result = $this->_service->create($params);
		}
		
		// Flash messages to session, and check for errors
		Session::flash('messages', $result->getMessages());
		if ($result->containsErrors()) {
			// Errors encountered - redisplay form with error messages
			return redirect()->back()->withInput();
		}
		
		// No errors - return to index page
		return redirect()->action('Administration\UsersController@index');
	}
}
