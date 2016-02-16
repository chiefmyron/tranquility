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
}
