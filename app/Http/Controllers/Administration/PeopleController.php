<?php namespace App\Http\Controllers\Administration;

use \Session as Session;
use \Response as Response;
use Illuminate\Http\Request as Request;
//use Illuminate\Support\Facades\Request as Request;
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
	 * Show a list of people
	 *
	 * @return Response
	 */
	public function index(Request $request) {
		// Get the list of people
		$response = $this->_person->all();
		$responseArray = $response->toArray();
		
		// Determine if we are using the detail or table view (default to table)
		$viewType = $request->input('view', $request->session()->get('people.index.viewType', 'table'));
		if ($viewType != 'detail' && $viewType != 'table') {
			// Default to table view
			$viewType = 'table';
		}
		$request->session()->put('people.index.viewType', $viewType);
		$responseArray['viewType'] = $viewType;
		
		// Display detail view
		if ($request->ajax()) {
			// AJAX response
			$ajax = new \Tranquility\View\AjaxResponse();
			$ajax->addContent('main-content-container', $this->_renderPartial('administration.people._partials.index-'.$viewType, $responseArray));
			$ajax->addContent('toolbar-container', $this->_renderPartial('administration.people._partials.toolbar-index-'.$viewType), 'attachCommonHandlers');
			return Response::json($ajax->toArray());
		}
		
		// Full page response
		return view('administration.people.index', $responseArray);
	}

	/**
	 * Show details of one specific person
	 *
	 * @param int $id  Entity ID of the person to show
	 * @return Response
	 */
	public function show($id) {
		$response = $this->_person->find($id);
		if ($response->containsErrors()) {
			// Redirect to index with error message
			Session::flash('messages', $response->getMessages());
			return redirect()->action('Administration\PeopleController@index');
		}
		return view('administration.people.show')->with('person', $response->getFirstContentItem());
	}
	
	/**
	 * Display page for creating a new person record
	 *
	 * @return Response
	 */
	public function create() {
		return view('administration.people.create');
	}
	
	/**
	 * Displays page for updating details of an existing person record
	 *
	 * @param int $id  Entity ID of the person to update
	 * @return Response
	 */
	public function update($id) {
		$response = $this->_person->find($id);
		if ($response->containsErrors()) {
			// Redirect to index with error message
			Session::flash('messages', $response->getMessages());
			return redirect()->action('Administration\PeopleController@index');
		}
		return view('administration.people.update')->with('person', $response->getFirstContentItem());
	}
	
	/**
	 * Store details of a new or updated person record
	 *
	 * @return Response
	 */
	public function store(Request $request) {
		// Save details of person
		$params = $request->all();
		$id = $request->input('id', 0);
		
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
	
	public function confirmAction() {
		$action = Request::input('action', null);
		switch($action) {
			case 'delete':
			
				break;
			case 'logout':
				
				break;
			case 'activate':
				
				break;
			case 'deactivate':
				
				break;
		}
	}
	
	public function delete($id) {
		
	}
}
