<?php namespace App\Http\Controllers\Administration;

use \Session as Session;
use \Response as Response;
use \Auth as Auth;
use Illuminate\Http\Request as Request;
use App\Http\Controllers\Controller;

use Carbon\Carbon;
use Tranquility\View\AjaxResponse as AjaxResponse;
use Tranquility\Services\AddressService as AddressService;
use Tranquility\Enums\System\EntityType as EnumEntityType;
use Tranquility\Enums\System\TransactionSource as EnumTransactionSource;
use Tranquility\Enums\System\MessageLevel as EnumMessageLevel;

class AddressController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Address controller
	|--------------------------------------------------------------------------
	|
	| This controller renders the administration interface for Addresses associated 
    | with other business objects
	|
	*/
	
    /**
     * Address service used for the majority of operations
     * @var \Tranquility\Services\AddressService
     */
	private $_service;

	/**
     * Constructor
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(AddressService $service) {
		$this->_service = $service;
	}

	/**
	 * Display a dialog containing a Google map iframe
	 *
	 * @return Response
	 */
	public function displayMap($id, Request $request) {
        // Ensure this is received as an ajax request only
		if (!$request->ajax()) {
			// TODO: Proper error handling here
			throw new Exception('Access only via AJAX request!');
		}
        
        // Retrieve address
        $response = $this->_service->find($id);
        if ($response->containsErrors()) {
            $ajax->addMessages($result->getMessages());
            $ajax->addContent('process-message-container', $this->_renderPartial('administration._partials.errors', ['messages' => $response->getMessages()]), 'showElement', array('process-message-container'));
            return $ajax;
        }
        
		// AJAX response
		$ajax = new \Tranquility\View\AjaxResponse();
		$dialog = $this->_renderPartial('administration.addresses._partials.dialogs.show-map', ['address' => $response->getFirstContentItem()]);
        $ajax->addContent('modal-content', $dialog, 'displayDialog');
		return Response::json($ajax->toArray());
	}
    
    /**
     * Show form for creating a new address
     *
     * @param int $parentId  Parent entity ID
     * @param Request $request
     * @return Response
     */
    public function create($parentId, Request $request) {
        // Ensure this is received as an ajax request only
		if (!$request->ajax()) {
			// TODO: Proper error handling here
			throw new Exception('Access only via AJAX request!');
		}
        
        // AJAX response
        $ajax = new \Tranquility\View\AjaxResponse();
        $dialog = $this->_renderPartial('administration.addresses._partials.dialogs.create-physical-address', ['parentId' => $parentId]);
        $ajax->addContent('modal-content', $dialog, 'displayDialog');
        return Response::json($ajax->toArray());
    }
	
	/**
	 * Displays page for updating details of an existing address
	 *
	 * @param int $id  Entity ID of the address to update
	 * @return Response
	 */
	public function update($id) {
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
		return redirect()->action('Administration\UsersController@showPersonUser', ['id' => $user->id]);
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
}
