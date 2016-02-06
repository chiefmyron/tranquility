<?php namespace App\Http\Controllers\Administration;

use \Session as Session;
use \Response as Response;
use \Auth as Auth;
use Illuminate\Http\Request as Request;
use App\Http\Controllers\Controller;

use Carbon\Carbon;
use Tranquility\View\AjaxResponse as AjaxResponse;
use Tranquility\Services\UserService as UserService;
use Tranquility\Services\PersonService as PersonService;
use Tranquility\Enums\System\EntityType as EnumEntityType;
use Tranquility\Enums\System\TransactionSource as EnumTransactionSource;
use Tranquility\Enums\System\MessageLevel as EnumMessageLevel;

class UsersController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Users controller
	|--------------------------------------------------------------------------
	|
	| This controller renders the administration interface for People with
    | user accounts, as well as application tokens
	|
	*/
	
    /**
     * Users service used for the majority of operations
     * @var \Tranquility\Services\UserService
     */
	private $_userService;
    
    /**
     * Person service used for the majority of operations
     * @var \Tranquility\Services\PersonService
     */
	private $_personService;

	/**
     * Constructor
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(UserService $userService, PersonService $personService) {
		$this->_userService = $userService;
        $this->_personService = $personService;
	}

	/**
	 * Show a list of people with user accounts
	 *
	 * @return Response
	 */
	public function listPeopleUsers(Request $request) {
        // Get pagination details from request
        $pageNumber = $request->get('page', 1);
        $recordsPerPage = $request->get('recordsPerPage', 20);
        
        // Get the list of people
		$response = $this->_userService->getPeopleWithUserAccounts(array(), array(), $pageNumber, $recordsPerPage);
		$responseArray = $response->toArray();
		
		// Full page response
		return view('administration.users.index', $responseArray);
	}

	/**
	 * Show details of one specific user
	 *
	 * @param int $id  Entity ID of the user to show
	 * @return Response
	 */
	public function showPersonUser($id) {
		$response = $this->_userService->find($id);
		if ($response->containsErrors()) {
			// Redirect to index with error message
			Session::flash('messages', $response->getMessages());
			return redirect()->action('Administration\UsersController@listPeopleUsers');
		}

        // Set flag to indicate if this is viewing the record for the current user
        $currentUser = ($id == Auth::user()->id);
        if ($currentUser) {
            $this->_addProcessMessage(EnumMessageLevel::Info, 'message_10034_user_viewing_own_record');
        }
		return view('administration.users.show', ['user' => $response->getFirstContentItem(), 'currentUser' => $currentUser]);
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
	 * Displays page for updating details of an existing user record
	 *
	 * @param int $id  Entity ID of the user to update
	 * @return Response
	 */
	public function updatePersonUser($id) {
		$response = $this->_userService->find($id);
		if ($response->containsErrors()) {
			// Redirect to index with error message
			Session::flash('messages', $response->getMessages());
			return redirect()->action('Administration\UsersController@listPeopleUsers');
		}
		return view('administration.users.update')->with('user', $response->getFirstContentItem());
	}
	
	/**
	 * Store details of a new or updated user record
	 *
	 * @return Response
	 */
	public function storePersonUser(Request $request) {
		// Save details of person
		$params = $request->all();
		$id = $request->input('id', 0);
		
		// Add in additional audit trail details
		$params['type'] = EnumEntityType::User;
		$params['updateBy'] = Auth::user();
		$params['updateReason'] = 'who knows?';
		$params['updateDateTime'] = Carbon::now();
		$params['transactionSource'] = EnumTransactionSource::UIBackend;
		
		// Create or update record		
		if ($id != 0) {
			$result = $this->_userService->update($id, $params);
		} else {
			$result = $this->_userService->create($params);
		}
		
		// Flash messages to session, and check for errors
		Session::flash('messages', $result->getMessages());
		if ($result->containsErrors()) {
			// Errors encountered - redisplay form with error messages
			return redirect()->back()->withInput();
		}
		
		// No errors - return to index page
		return redirect()->action('Administration\UsersController@showPersonUser', ['id' => $result->getFirstContentItem()->id]);
	}
    
    public function changePassword(Request $request) {
        
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
    
    public function showUser($id, Request $request) {
		// Ensure this is received as an ajax request only
		if (!$request->ajax()) {
			// TODO: Proper error handling here
			throw new Exception('Access only via AJAX request!');
		}
        
        // Get inputs from request and retrieve person record
        $ajax = new AjaxResponse();
        $response = $this->_person->find($id);
        if ($response->containsErrors()) {
            $ajax->addMessages($result->getMessages());
            $ajax->addContent('process-message-container', $this->_renderPartial('administration._partials.errors', ['messages' => $response->getMessages()]), 'showElement', array('process-message-container'));
            return $ajax;
        }
        
        // Make sure person has a user account
        $person = $response->getFirstContentItem();
        if (!$person->getUserAccount()) {
            throw new Exception('Person must have a user account');
        }
        
        // Render dialog
        $dialog = $this->_renderPartial('administration.users._partials.dialogs.view-details', $person->getUserAccount());
        $ajax->addContent('modal-content', $dialog, 'displayDialog');
		return Response::json($ajax->toArray());
	}
}
