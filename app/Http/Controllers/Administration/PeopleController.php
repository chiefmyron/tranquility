<?php namespace App\Http\Controllers\Administration;

use \Session as Session;
use \Response as Response;
use \Auth as Auth;
use Illuminate\Http\Request as Request;
use App\Http\Controllers\Controller;

use Carbon\Carbon;
use Tranquility\Services\PersonService as PersonService;
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
	}

	/**
	 * Show a list of people
	 *
	 * @return Response
	 */
	public function index(Request $request) {
        // Get pagination details from request
        $pageNumber = $request->get('page', 1);
        $recordsPerPage = $request->get('recordsPerPage', 20);
        
		// Get the list of people
		$response = $this->_person->all(array(), array(), $pageNumber, $recordsPerPage);
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
			$ajax->addContent('toolbar-container', $this->_renderPartial('administration.people._partials.toolbars.index-'.$viewType), 'attachCommonHandlers');
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
		$params['updateBy'] = Auth::user();
		$params['updateReason'] = 'who knows?';
		$params['updateDateTime'] = Carbon::now();
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
	
	public function confirmAction(Request $request) {
		// Ensure this is received as an ajax request only
		if (!$request->ajax()) {
			// TODO: Proper error handling here
			throw new Exception('Access only via AJAX request!');
		}
        
        // Get inputs from request
        $id = $request->input('id', array());
        $action = $request->input('action', null);
        
        // Retrieve list of IDs to confirm action against
        $person = null;
        if (!is_array($id) && ($id > 0)) {
            // Confirmation is for a single person only - retrieve details
            $response = $this->_person->find($id);
            if ($response->containsErrors()) {
                // TODO: Proper error handling here
                throw new Exception('Error:'.$response->getMessages());
    		}
            $person = $response->getFirstContentItem();
            $id = array($id);
        }
        
        $data = array(
            'selectedIds' => $id,
            'person' => $person
        );
        
        // Render dialog based on action
		$dialog = "";
		switch($action) {
			case 'delete':
                $dialog = $this->_renderPartial('administration.people._partials.dialogs.confirm-delete', $data);
				break;
			case 'logout':
				$dialog = $this->_renderPartial('administration.people._partials.dialogs.confirm-logout', $data);
				break;
			case 'activate':
				$dialog = $this->_renderPartial('administration.people._partials.dialogs.confirm-activate', $data);
				break;
			case 'deactivate':
				$dialog = $this->_renderPartial('administration.people._partials.dialogs.confirm-deactivate', $data);
				break;
		}
		
		// AJAX response
		$ajax = new \Tranquility\View\AjaxResponse();
		$ajax->addContent('modal-content', $dialog, 'displayDialog');
		return Response::json($ajax->toArray());
	}
    
	public function delete(Request $request) {
		// Get array of IDs to work with from input
        $inputIds = $request->input('id', array());
        
        // Set up audit trail details
        $params = array();
        $params['updateBy'] = Auth::user();
        $params['updateReason'] = 'who knows?';
        $params['updateDateTime'] = Carbon::now();
        $params['transactionSource'] = EnumTransactionSource::UIBackend;
        if (count($inputIds) > 1) {
            $response = $this->_person->deleteMultiple($inputIds, $params);
        } else {
            $response = $this->_person->delete($inputIds[0], $params);
        }
        
        // If AJAX request, send response
        if ($request->ajax()) {
            // Refresh index view
            $responseArray = $this->_person->all()->toArray();
            $responseArray['viewType'] = $request->session()->get('people.index.viewType', 'table');;

			// AJAX response
			$ajax = new \Tranquility\View\AjaxResponse();
            $ajax->addCallback('hideElement', array('process-message-container'));
			$ajax->addContent('main-content-container', $this->_renderPartial('administration.people._partials.index-'.$responseArray['viewType'], $responseArray));
			$ajax->addContent('toolbar-container', $this->_renderPartial('administration.people._partials.toolbars.index-'.$responseArray['viewType']), 'attachCommonHandlers');
            $ajax->addContent('process-message-container', $this->_renderPartial('administration._partials.errors', ['messages' => $response->getMessages()]), 'showElement', array('process-message-container'));
            $ajax->addCallback('closeDialog');
			return Response::json($ajax->toArray());
		}
		
		// Not AJAX request - redirect to index page
        Session::flash('messages', $response->getMessages());
        if ($response->containsErrors()) {
            // Errors encountered - redisplay form with error messages
			return redirect()->back()->withInput();
        } else {
            // No errors - return to list of people
            return redirect()->action('Administration\PeopleController@index');
        }
		
	}
}
