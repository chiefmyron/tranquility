<?php namespace Tranquility\Http\Controllers\Administration;

use \Session as Session;
use \Response as Response;
use \Auth as Auth;
use Illuminate\Http\Request as Request;

use Carbon\Carbon;
use Tranquility\Utility;
use Tranquility\View\AjaxResponse                  as AjaxResponse;
use Tranquility\Services\AccountService            as AccountService;
use Tranquility\Services\AddressService            as AddressService;
use Tranquility\Enums\System\EntityType            as EnumEntityType;
use Tranquility\Enums\System\MessageLevel          as EnumMessageLevel;
use Tranquility\Enums\System\TransactionSource     as EnumTransactionSource;
use Tranquility\Enums\BusinessObjects\Address\AddressTypes as EnumAddressType;

class AccountsController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Account controller
	|--------------------------------------------------------------------------
	|
	| This controller handles requests relating to customer accounts
	|
	*/
	
	private $_accountService;
    private $_addressService;

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(AccountService $account, AddressService $address) {
		$this->_accountService = $account;
        $this->_addressService = $address;
	}

	/**
	 * Show a list of Accounts
	 *
	 * @return Response
	 */
	public function index(Request $request) {
        // Get pagination details from request
        $pageNumber = $request->get('page', 1);
        $recordsPerPage = $request->get('recordsPerPage', 20);
        
		// Get the list of people
		$response = $this->_accountService->all(array(), array(), $recordsPerPage, $pageNumber);
        $responseArray = array(
            'accounts' => $response->getContent()
        );

		// Display detail view
		if ($request->ajax()) {
			// AJAX response
			$ajax = new \Tranquility\View\AjaxResponse();
			$ajax->addContent('#main-content-container', $this->_renderPartial('administration.accounts._partials.panels.list-table', $responseArray));
			return Response::json($ajax->toArray());
		}
		
		// Full page response
		return view('administration.accounts.index', $responseArray);
	}

	/**
	 * Show details of one specific Account
	 *
	 * @param int $id  Entity ID of the Account to show
	 * @return Response
	 */
	public function show($id) {
		$response = $this->_accountService->find($id);
		if ($response->containsErrors()) {
			// Redirect to index with error message
			Session::flash('messages', $response->getMessages());
			return redirect()->action('Administration\AccountsController@index');
		}
        
        // Display Account details
		return view('administration.accounts.show')->with('account', $response->getFirstContentItem());
	}
	
	/**
	 * Display dialog for creating a new Account record
	 *
	 * @return Response
	 */
	public function create(Request $request) {
		// If called via AJAX, display as a dialog
		if ($request->ajax()) {
			// Render dialog
			$ajax = new AjaxResponse();
			$dialog = $this->_renderPartial('administration.accounts._partials.dialogs.create');
			$ajax->addContent('#modal-content', $dialog, 'core.displayDialog');
			return Response::json($ajax->toArray());
		}
        
        // Display full page
		return view('administration.accounts.create');
	}
	
	/**
	 * Displays page for updating details of an existing Account record
	 *
	 * @param int $id  Entity ID of the Account to update
	 * @return Response
	 */
	public function update(Request $request, $id) {
		// Retrieve account details
		$response = $this->_accountService->find($id);
		if ($response->containsErrors()) {
			// Redirect to index with error message
			Session::flash('messages', $response->getMessages());
			return redirect()->action('Administration\AccountsController@index');
		}
		$data = array('account' => $response->getFirstContentItem());
		
		// If called via AJAX, display as a dialog
		if ($request->ajax()) {
			// Render dialog
			$ajax = new AjaxResponse();
			$dialog = $this->_renderPartial('administration.accounts._partials.dialogs.update', $data);
			$ajax->addContent('#modal-content', $dialog, 'core.displayDialog');
			return Response::json($ajax->toArray());
		}
        
        // Display full page
		return view('administration.accounts.update')->with($data);
	}
	
	/**
	 * Store details of a new or updated Account record
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
            $params['updateReason'] = 'backend account update';
			$result = $this->_accountService->update($id, $params);
		} else {
            $params['updateReason'] = 'backend account create';
			$result = $this->_accountService->create($params);
		}

		// If errors were encountered, display on the form
		if ($result->containsErrors()) {
			return $this->_renderFormErrors($request, $result->getMessages());
		}
		
		// Show updated record
		$account = $result->getFirstContentItem();
		if ($request->ajax()) {
			// Render address panel for parent entity
			$heading = $this->_renderPartial('administration._partials.heading', ['heading' => $account->name]);
			$content = $this->_renderPartial('administration.accounts._partials.content.show', ['account' => $account]);
			$messages = $this->_renderPartial('administration._partials.errors', ['messages' => $result->getMessages()]);

			$ajax = new AjaxResponse(); 
			$ajax->addContent('#page-header .page-title', $heading);
			$ajax->addContent('#main-content-container', $content);
			$ajax->addContent('#process-message-container', $messages, 'core.showElement', array('process-message-container'));
			$ajax->addCallback('core.closeDialog');
			return Response::json($ajax->toArray());
		}

		Session::flash('messages', $result->getMessages());		
		return redirect()->action('Administration\AccountsController@show', ['id' => $account->id]);
	}
	
    /**
	 * Show dialog requesting confirmation of delete action
	 *
	 * @return Response
	 */
	public function confirmDelete(Request $request) {
		// Ensure this is received as an ajax request only
		if (!$request->ajax()) {
			// TODO: Proper error handling here
			throw new Exception('Access only via AJAX request!');
		}
        
        // Get inputs from request
        $id = $request->input('id', array());
        
        // Retrieve list of IDs to confirm action against
        $account = null;
        if (!is_array($id) && ($id > 0)) {
            // Confirmation is for a single Account only - retrieve details
            $result = $this->_accountService->find($id);
            if ($result->containsErrors()) {
				// Display error to user
				return $this->_renderFormErrors($request, $result->getMessages());
    		}
            $account = $result->getFirstContentItem();
            $id = array($id);
        }
        
        // Render dialog
		$data = array('selectedIds' => $id, 'account' => $account);
		$dialog = $this->_renderPartial('administration.accounts._partials.dialogs.confirm-delete', $data);

		// AJAX response
		$ajax = new \Tranquility\View\AjaxResponse();
		$ajax->addContent('#modal-content', $dialog, 'core.displayDialog');
		return Response::json($ajax->toArray());
	}
    
    /**
	 * Delete specified Account record
	 *
	 * @return Response
	 */
	public function delete(Request $request) {
		// Get array of IDs to work with from input
        $inputIds = $request->input('id', array());
        
        // Set up audit trail details
        $params = array();
        $params['updateBy'] = Auth::user();
        $params['updateReason'] = 'backend account delete';
        $params['updateDateTime'] = Carbon::now();
        $params['transactionSource'] = EnumTransactionSource::UIBackend;
        if (count($inputIds) > 1) {
            $response = $this->_accountService->deleteMultiple($inputIds, $params);
        } else {
            $response = $this->_accountService->delete($inputIds[0], $params);
        }
        
        // If AJAX request, send response
        if ($request->ajax()) {
			// Get the existing messages from the 'delete' service call
			$deleteMessages = $response->getMessages();

			// Refresh index view
            $pageNumber = $request->get('page', 1);
            $recordsPerPage = $request->get('recordsPerPage', 20);
            $response = $this->_accountService->all(array(), array(), $recordsPerPage, $pageNumber);

			// Merge message arrays
			$messages = array_merge($deleteMessages, $response->getMessages());

			// AJAX response
			$ajax = new \Tranquility\View\AjaxResponse();
            $ajax->addCallback('core.hideElement', array('process-message-container'));
			$ajax->addContent('#main-content-container', $this->_renderPartial('administration.accounts._partials.panels.index', ['accounts' => $response->getContent()]));
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
            return redirect()->action('Administration\AccountsController@index');
        }
	}
}
