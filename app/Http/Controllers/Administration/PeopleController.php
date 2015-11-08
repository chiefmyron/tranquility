<?php namespace App\Http\Controllers\Administration;

use \Session as Session;
use \Response as Response;
use Illuminate\Support\Facades\Request as Request;
use App\Http\Controllers\Controller;

use Carbon\Carbon;
use Tranquility\Services\Person as PersonService;
use Tranquility\Enums\System\EntityType as EnumEntityType;
use Tranquility\Enums\System\TransactionSource as EnumTransactionSource;

class PeopleController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| People controller
	|--------------------------------------------------------------------------
	|
	| This controller renders the "marketing page" for the application and
	| is configured to only allow guests. Like most of the other sample
	| controllers, you are free to modify or remove it as you desire.
	|
	*/
	
	private $_person;

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(PersonService $person) {
		$this->_person = $person;
		//$this->middleware('guest');
	}

	/**
	 * Show the application welcome screen to the user.
	 *
	 * @return Response
	 */
	public function index() {
		// Get the list of people
		$response = $this->_person->all();
		$responseArray = $response->toArray();
		
		// Determine if we are using the detail or table view (default to table)
		$viewType = Request::input('view', 'detail');
		if ($viewType != 'detail' && $viewType != 'table') {
			$viewType = 'table';
		}
		$responseArray['viewType'] = $viewType;
		
		// Display detail view
		if (Request::ajax()) {
			// AJAX response
			$ajax = new \Tranquility\View\AjaxResponse();
			$ajax->addContent('main-content-container', $this->_renderPartial('administration.people._partials.index-'.$viewType, $responseArray));
			$ajax->addContent('toolbar-container', $this->_renderPartial('administration.people._partials.toolbar-index-'.$viewType), 'attachCommonHandlers');
			return Response::json($ajax->toArray());
		}
		
		// Full page response
		return view('administration.people.index', $responseArray);
	}
	

	
	public function show($id) {
		$response = $this->_person->find($id);
		if ($response->containsErrors()) {
			// Redirect to index with error message
			Session::flash('messages', $response->getMessages());
			return redirect()->action('Administration\PeopleController@index');
		}
		return view('administration.people.show')->with('person', $response->getFirstContentItem());
	}
	
	public function create() {
		return view('administration.people.create');
	}
	
	public function update($id) {
		$response = $this->_person->find($id);
		if ($response->containsErrors()) {
			// Redirect to index with error message
			Session::flash('messages', $response->getMessages());
			return redirect()->action('Administration\PeopleController@index');
		}
		return view('administration.people.update')->with('person', $response->getFirstContentItem());
	}
	
	public function store() {
		// Save details of person
		$params = Request::all();
		$id = Request::input('id', 0);
		
		// Add in additional audit trail details
		$params['type'] = EnumEntityType::Person;
		$params['updateBy'] = 1;
		$params['updateReason'] = 'who knows?';
		$params['updateDatetime'] = Carbon::now();
		$params['transactionSource'] = EnumTransactionSource::UIBackend;
		
		// Create or update record		
		if ($id != 0) {
			$result = $this->_person->update($id, $params);
		} else {
			$result = $this->_person->create($params);
		}
		
		// Flash messages to session, and check for errors
		Session::flash('messages', $result->getMessages());
		if ($result->containsErrors()) {
			// Errors encountered - redisplay form with error messages
			return redirect()->back()->withInput();
		}
		
		// No errors - return to index page
		return redirect()->action('Administration\PeopleController@index');
	}
}
