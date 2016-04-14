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
	public function index(Request $request) {
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
	public function show($id) {
		$response = $this->_userService->find($id);
		if ($response->containsErrors()) {
			// Redirect to index with error message
			Session::flash('messages', $response->getMessages());
			return redirect()->action('Administration\UsersController@index');
		}

        // Set flag to indicate if this is viewing the record for the current user
        $currentUser = ($id == Auth::user()->id);
        $messages = Session::get('messages');
        if ($currentUser && (count($messages) <= 0)) {
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
	public function update($id) {
		$response = $this->_userService->find($id);
		if ($response->containsErrors()) {
			// Redirect to index with error message
			Session::flash('messages', $response->getMessages());
			return redirect()->action('Administration\UsersController@index');
		}
		return view('administration.users.update')->with('user', $response->getFirstContentItem());
	}
	
	/**
	 * Store details of a new or updated user record
	 *
	 * @return Response
	 */
	public function store(Request $request) {
		// Save details of user
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
		
		// No errors - update session variables and return to user page
        $user = $result->getFirstContentItem();
        Session::set('tranquility.localeFormatCode', $user->localeCode);
        Session::set('tranquility.timezoneFormatCode', $user->timezoneCode);
		return redirect()->action('Administration\UsersController@show', ['id' => $user->id]);
	}
    
    /** 
     * Delete one or more user accounts
     *
     * @return Response
     */
    public function delete(Request $request) {
        $id = $request->input('id', array());
        if (count($id) > 1) {
            return $this->_deleteMultiple($request);
        }
        
        // Handle single user deletion
        $params = array();
		$params['type'] = EnumEntityType::User;
		$params['updateBy'] = Auth::user();
		$params['updateReason'] = 'delete single user';
		$params['updateDateTime'] = Carbon::now();
		$params['transactionSource'] = EnumTransactionSource::UIBackend;
        $result = $this->_userService->delete($id[0], $params);
        
        // Flash messages to session, and check for errors
		Session::flash('messages', $result->getMessages());
		if ($result->containsErrors()) {
			// Errors encountered - redisplay form with error messages
			return redirect()->back()->withInput();
		}
		
		// No errors - return to user list page
		return redirect()->action('Administration\UsersController@index');
    }
    
    public function changePassword($id, Request $request) {
        // Ensure this is received as an ajax request only
		if (!$request->ajax()) {
			// TODO: Proper error handling here
			throw new Exception('Access only via AJAX request!');
		}
        
		// AJAX response
		$ajax = new \Tranquility\View\AjaxResponse();
		$dialog = $this->_renderPartial('administration.users._partials.dialogs.change-password', ['id' => $id]);
        $ajax->addContent('modal-content', $dialog, 'displayDialog');
		return Response::json($ajax->toArray());
    }
    
    public function saveNewPassword(Request $request) {
        // Save details of user
		$params = $request->all();
		$id = $request->input('id', 0);
		
		// Add in additional audit trail details
		$params['type'] = EnumEntityType::User;
		$params['updateBy'] = Auth::user();
		$params['updateReason'] = 'who knows?';
		$params['updateDateTime'] = Carbon::now();
		$params['transactionSource'] = EnumTransactionSource::UIBackend;
		
		// Change password for the user
        $response = $this->_userService->changePassword($id, $params);
        
        // Set up response
        $ajax = new \Tranquility\View\AjaxResponse();
        if ($response->containsErrors()) {
			// Errors encountered - redisplay form with error messages
            $ajax->addContent('modal-dialog-container #process-message-container', $this->_renderPartial('administration._partials.errors', ['messages' => $response->getMessages()]), 'showElement', array('modal-dialog-container #process-message-container'));
			$ajax->addMessages($response->getMessages());
            return Response::json($ajax->toArray());
		}
        
        // Success response
        $ajax = new \Tranquility\View\AjaxResponse();
        $ajax->addCallback('hideElement', array('process-message-container'));
        $ajax->addContent('main-content-container', $this->_renderPartial('administration.users._partials.panels.user-details', ['user' => $response->getFirstContentItem()]));
        $ajax->addContent('process-message-container', $this->_renderPartial('administration._partials.errors', ['messages' => $response->getMessages()]), 'showElement', array('process-message-container'));
        $ajax->addCallback('closeDialog');
        return Response::json($ajax->toArray());
    }
    
    /**
     * Displays confirmation dialog when user attempts to delete or suspend
     * one or more user accounts
     *
     * @param Request $request
     * @return View
     */
    public function confirm($id, Request $request) {
		// Ensure this is received as an ajax request only
		if (!$request->ajax()) {
			// TODO: Proper error handling here
			throw new Exception('Access only via AJAX request!');
		}
        
        // Get inputs from request
        $action = $request->input('action', null);
        
        // Retrieve user
        $ajax = new \Tranquility\View\AjaxResponse();
        $response = $this->_userService->find($id);
        $contentItems = $response->getContent();
        if ($response->containsErrors() || count($response->getContent()) == 0) {
            // Errors encountered - redisplay form with error messages
            $ajax->addContent('process-message-container', $this->_renderPartial('administration._partials.errors', ['messages' => $response->getMessages()]), 'showElement', array('process-message-container'));
			$ajax->addMessages($response->getMessages());
            return Response::json($ajax->toArray());
        }
        $data = array('user' => $response->getFirstContentItem());
        
        // Render dialog based on action
        switch($action) {
            case 'delete':
                $dialog = $this->_renderPartial('administration.users._partials.dialogs.confirm-single-delete', $data);
				break;
			case 'logout':
				$dialog = $this->_renderPartial('administration.people._partials.dialogs.confirm-single-logout', $data);
				break;
			case 'activate':
				$dialog = $this->_renderPartial('administration.people._partials.dialogs.confirm-single-activate', $data);
				break;
			case 'deactivate':
				$dialog = $this->_renderPartial('administration.people._partials.dialogs.confirm-single-deactivate', $data);
				break;
            default:
                $dialog = '';
                break;
        }
        
        // Display dialog
        $user = $response->getFirstContentItem();
		$ajax->addContent('modal-content', $dialog, 'displayDialog');
		return Response::json($ajax->toArray());
	}
}
