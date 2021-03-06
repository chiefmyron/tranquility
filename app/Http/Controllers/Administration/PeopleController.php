<?php namespace App\Http\Controllers\Administration;

use \Session as Session;
use \Response as Response;
use \Auth as Auth;
use Illuminate\Http\Request as Request;
use App\Http\Controllers\Administration\Controller;

use Carbon\Carbon;
use Tranquility\Utility;
use Tranquility\View\AjaxResponse                  as AjaxResponse;
use Tranquility\Services\UserService               as UserService;
use Tranquility\Services\PersonService             as PersonService;
use Tranquility\Services\AddressService            as AddressService;
use Tranquility\Enums\System\EntityType            as EnumEntityType;
use Tranquility\Enums\System\MessageLevel          as EnumMessageLevel;
use Tranquility\Enums\System\TransactionSource     as EnumTransactionSource;
use Tranquility\Enums\BusinessObjects\Address\AddressTypes as EnumAddressType;

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
	
	private $_service;
    private $_userService;
    private $_addressService;

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(PersonService $person, AddressService $address, UserService $user) {
		$this->_service = $person;
        $this->_userService = $user;
        $this->_addressService = $address;
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
		$response = $this->_service->all(array(), array(), $recordsPerPage, $pageNumber);
        $responseArray = array(
            'people' => $response->getContent()
        );

		// Display detail view
		if ($request->ajax()) {
			// AJAX response
			$ajax = new \Tranquility\View\AjaxResponse();
			$ajax->addContent('#main-content-container', $this->_renderPartial('administration.people._partials.panels.list-table', $responseArray));
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
		$response = $this->_service->find($id);
		if ($response->containsErrors()) {
			// Redirect to index with error message
			Session::flash('messages', $response->getMessages());
			return redirect()->action('Administration\PeopleController@index');
		}
        
        // Check to see if person is viewing their own record
        $person = $response->getFirstContentItem();
        $user = $person->getUserAccount();
        $messages = Session::get('messages');
        if (!is_null($user) && ($user->id == Auth::user()->id) && (count($messages) <= 0)) {
            $this->_addProcessMessage(EnumMessageLevel::Info, 'message_10034_user_viewing_own_record');
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
	 * Displays page for updating details of an existing Person record
	 *
	 * @param int $id  Entity ID of the Person to update
	 * @return Response
	 */
	public function update(Request $request, $id) {
		// Retrieve account details
		$response = $this->_service->find($id);
		if ($response->containsErrors()) {
			// Redirect to index with error message
			Session::flash('messages', $response->getMessages());
			return redirect()->action('Administration\PeopleController@index');
		}
		$data = array('person' => $response->getFirstContentItem());
		
		// If called via AJAX, display as a dialog
		if ($request->ajax()) {
			// Render dialog
			$ajax = new AjaxResponse();
			$dialog = $this->_renderPartial('administration.people._partials.dialogs.update', $data);
			$ajax->addContent('#modal-content', $dialog, 'core.displayDialog');
			return Response::json($ajax->toArray());
		}
        
        // Display full page
		return view('administration.peoplle.update')->with($data);
	}
	
	/**
	 * Store details of a new or updated Person record
	 *
	 * @return Response
	 */
	public function store(Request $request) {
		// Save details of person
		$params = $request->all();
		$id = $request->input('id', 0);

		// Add in additional audit trail details
		$params['type'] = EnumEntityType::Account;
		$params['updateBy'] = Auth::user();
		$params['updateDateTime'] = Carbon::now();
		$params['transactionSource'] = EnumTransactionSource::UIBackend;
		
		// Create or update record		
		if ($id != 0) {
            $params['updateReason'] = 'backend person update';
			$result = $this->_service->update($id, $params);
		} else {
            $params['updateReason'] = 'backend person create';
			$result = $this->_service->create($params);
		}

		// If errors were encountered, display on the form
		if ($result->containsErrors()) {
			return $this->_renderFormErrors($request, $result->getMessages());
		}
		
		// Show updated record
		$person = $result->getFirstContentItem();
		if ($request->ajax()) {
			// Render address panel for parent entity
			$heading = $this->_renderPartial('administration._partials.heading', ['heading' => $person->getFullName()]);
			$content = $this->_renderPartial('administration.people._partials.content.show', ['person' => $person]);
			$messages = $this->_renderPartial('administration._partials.errors', ['messages' => $result->getMessages()]);

			$ajax = new AjaxResponse(); 
			$ajax->addContent('#page-header .page-title', $heading);
			$ajax->addContent('#main-content-container', $content);
			$ajax->addContent('#process-message-container', $messages, 'core.showElement', array('process-message-container'));
			$ajax->addCallback('core.closeDialog');
			return Response::json($ajax->toArray());
		}

		Session::flash('messages', $result->getMessages());		
		return redirect()->action('Administration\PeopleController@show', ['id' => $person->id]);
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
            $response = $this->_service->find($id);
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
		$ajax->addContent('#modal-content', $dialog, 'core.displayDialog');
		return Response::json($ajax->toArray());
	}
    
	public function delete(Request $request) {
		// Get array of IDs to work with from input
        $inputIds = $request->input('id', array());
        
        // Set up audit trail details
        $params = array();
        $params['updateBy'] = Auth::user();
        $params['updateReason'] = 'backend user delete';
        $params['updateDateTime'] = Carbon::now();
        $params['transactionSource'] = EnumTransactionSource::UIBackend;
        if (count($inputIds) > 1) {
            $response = $this->_service->deleteMultiple($inputIds, $params);
        } else {
            $response = $this->_service->delete($inputIds[0], $params);
        }
        
        // If AJAX request, send response
        if ($request->ajax()) {
			// Get the existing messages from the 'delete' service call
			$deleteMessages = $response->getMessages();

            // Refresh index view
            $pageNumber = $request->get('page', 1);
            $recordsPerPage = $request->get('recordsPerPage', 20);
            $response = $this->_service->all(array(), array(), $recordsPerPage, $pageNumber);

			// Merge message arrays
			$messages = array_merge($deleteMessages, $response->getMessages());

			// AJAX response
			$ajax = new \Tranquility\View\AjaxResponse();
            $ajax->addCallback('core.hideElement', array('process-message-container'));
			$ajax->addContent('#main-content-container', $this->_renderPartial('administration.people._partials.panels.list-table', ['people' => $response->getContent()]));
            $ajax->addContent('#process-message-container', $this->_renderPartial('administration._partials.errors', ['messages' => $messages]), 'core.showElement', array('process-message-container'));
            $ajax->addCallback('core.closeDialog');
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
    
    public function createUser($id, Request $request) {
        // Ensure this is received as an ajax request only
		if (!$request->ajax()) {
			// TODO: Proper error handling here
			throw new Exception('Access only via AJAX request!');
		}
        
        // Get existing person record
        $ajax = new AjaxResponse();
        $response = $this->_service->find($id);
        if ($response->containsErrors()) {
            $ajax->addMessages($result->getMessages());
            $ajax->addContent('#process-message-container', $this->_renderPartial('administration._partials.errors', ['messages' => $response->getMessages()]), 'core.showElement', array('process-message-container'));
            return $ajax;
        }
        
        // Check if person already has a user account
        $person = $response->getFirstContentItem();
        $user = $person->getUserAccount();
        if (!is_null($user)) {
            $ajax->addMessage(10034, 'message_10033_user_already_exists', EnumMessageLevel::Error, null);
            $ajax->addContent('#process-message-container', $this->_renderPartial('administration._partials.errors', ['messages' => $response->getMessages()]), 'core.showElement', array('process-message-container'));
        }
        
        // Get any existing email addresses for the person
        $emailAddresses = array();
        foreach ($person->getAddresses(EnumAddressType::Email) as $address) {
            $emailAddresses[$address->addressText] = $address->addressText;
        }
        
        // Render dialog
        $dialog = $this->_renderPartial('administration.people._partials.dialogs.create-user', ['person' => $person, 'emailAddresses' => $emailAddresses]);
        $ajax->addContent('#modal-content', $dialog, 'core.displayDialog');
		return Response::json($ajax->toArray());
    }
    
    public function storeUser($id, Request $request) {
        // Ensure this is received as an ajax request only
		if (!$request->ajax()) {
			// TODO: Proper error handling here
			throw new Exception('Access only via AJAX request!');
		}
        
        // Get input values
        $data = $request->all();
        $data['parentId'] = $id;
        $data['registeredDateTime'] = Carbon::now();
        
        // Set audit trail details
        $auditTrail = array (
            'updateBy' => Auth::user(),
            'updateDateTime' => Carbon::now(),
            'updateReason' => 'new user creation',
            'transactionSource' => EnumTransactionSource::UIBackend,
        );
        
        // Check if a new email address needs to be created
        $ajax = new \Tranquility\View\AjaxResponse();
        if (Utility::extractValue($data, 'usernameOption', 'new') == 'new') {
            // Retrieve the person record
            $person = $this->_service->find($id)->getFirstContentItem();
            
            // Check to see if the address already exists for the person
            $addressText = Utility::extractValue($data, 'addressText', '');
            $criteria = array(['addressText', '=', $addressText], ['parentEntity', '=', $person]);
            $response = $this->_addressService->all($criteria);
            if ($response->getItemCount() <= 0) {
                // Create new email address
                $emailData = array(
                    'addressType' => 'other',
                    'addressText' => $addressText,
                    'primaryContact' => 0,
                    'category' => EnumAddressType::Email,
                    'parentId' => $id,
                );
                $emailData = array_merge($emailData, $auditTrail);
                $response = $this->_addressService->create($emailData);
                if ($response->containsErrors()) {
                    // Errors encountered - redisplay form with error messages
                    $ajax->addContent('#modal-dialog-container #process-message-container', $this->_renderPartial('administration._partials.errors', ['messages' => $response->getMessages()]), 'core.showElement', array('modal-dialog-container #process-message-container'));
                    $ajax->addMessages($this->_renderInlineMessages($response->getMessages()));
                    return Response::json($ajax->toArray());
                }
            }
            // Set username to new email address
            $data['username'] = $addressText;    
        } else {
            // Set username to use existing email address
            $data['username'] = Utility::extractValue($data, 'existingUsername', '');
        }
        
        // Create new user
        unset($data['usernameOption'], $data['existingUsername'], $data['newUsername']);
        $data = array_merge($data, $auditTrail);
        $response = $this->_userService->create($data);
        if ($response->containsErrors()) {
            // Errors encountered - redisplay form with error messages
            $ajax->addContent('#modal-dialog-container #process-message-container', $this->_renderPartial('administration._partials.errors', ['messages' => $response->getMessages()]), 'core.showElement', array('modal-dialog-container #process-message-container'));
            $ajax->addMessages($this->_renderInlineMessages($response->getMessages()));
            return Response::json($ajax->toArray());
        }
        
        // Render address panel for person
        $person = $this->_service->find($id)->getFirstContentItem();
        $ajax->addContent('#person-details-container', $this->_renderPartial('administration.people._partials.panels.person-details', ['person' => $person, 'user' => $person->getUserAccount(), 'account' => $person->getAccount()]));
        $ajax->addContent('#email-addresses-container', $this->_renderPartial('administration.addresses._partials.panels.email-address', ['addresses' => $person->getAddresses(EnumAddressType::Email), 'parentId' => $person->id]));
        $ajax->addContent('#process-message-container', $this->_renderPartial('administration._partials.errors', ['messages' => $response->getMessages()]), 'core.showElement', array('process-message-container'));
        $ajax->addCallback('core.closeDialog');
        return Response::json($ajax->toArray());
    }
    
    public function showUser($id, Request $request) {
		// Ensure this is received as an ajax request only
		if (!$request->ajax()) {
			// TODO: Proper error handling here
			throw new Exception('Access only via AJAX request!');
		}
        
        // Get inputs from request and retrieve person record
        $ajax = new AjaxResponse();
        $response = $this->_service->find($id);
        if ($response->containsErrors()) {
            $ajax->addMessages($result->getMessages());
            $ajax->addContent('#process-message-container', $this->_renderPartial('administration._partials.errors', ['messages' => $response->getMessages()]), 'core.showElement', array('process-message-container'));
            return $ajax;
        }
        
        // Make sure person has a user account
        $person = $response->getFirstContentItem();
        if (!$person->getUserAccount()) {
            throw new Exception('Person must have a user account');
        }
        
        // Render dialog
        $dialog = $this->_renderPartial('administration.users._partials.dialogs.view-details', $person->getUserAccount());
        $ajax->addContent('#modal-content', $dialog, 'core.displayDialog');
		return Response::json($ajax->toArray());
	}
}
