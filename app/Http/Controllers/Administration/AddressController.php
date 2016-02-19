<?php namespace App\Http\Controllers\Administration;

use \Session as Session;
use \Response as Response;
use \Auth as Auth;
use Illuminate\Http\Request as Request;
use App\Http\Controllers\Controller;

use Carbon\Carbon;
use Tranquility\View\AjaxResponse as AjaxResponse;
use Tranquility\Services\AddressService as AddressService;
use Tranquility\Enums\BusinessObjects\Address\AddressTypes as EnumAddressType;


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
        $ajax->addContent('modal-content', $dialog, 'displayDialog', [null, "large"]);
		return Response::json($ajax->toArray());
	}
    
    /**
     * Show form for adding a new address
     *
     * @param int     $parentId  Parent entity ID
     * @param string  $type      Address type (physical, telephone, electronic)
     * @param Request $request
     * @return Response
     */
    public function create($type, Request $request) {
        // Ensure this is received as an ajax request only
		if (!$request->ajax()) {
			// TODO: Proper error handling here
			throw new Exception('Access only via AJAX request!');
		}
        
        // Validate address type
        $entityType = '';
        switch ($type) {
            case EnumAddressType::Physical:
                $entityType = EnumEntityType::AddressPhysical;
                break;
            case EnumAddressType::Phone:
                $entityType = EnumEntityType::AddressPhone;
                break;
            case EnumAddressType::Electronic:
                $entityType = EnumEntityType::AddressElectronic;
                break;
        }
        
        // Setup data for view
        $data = array(
            'parentId' => $request->input('parentId', 0),
            'type' => $entityType
        );
        $ajax = new \Tranquility\View\AjaxResponse();
        $dialog = $this->_renderPartial('administration.addresses._partials.dialogs.create-address-'.$type, $data);
        $ajax->addContent('modal-content', $dialog, 'displayDialog');
        return Response::json($ajax->toArray());
    }
    
    /**
     * Show form for updating an existing physical address
     *
     * @param int $id        Address entity ID
     * @return Response
     */
    public function update($id, Request $request) {
        // Ensure this is received as an ajax request only
		if (!$request->ajax()) {
			// TODO: Proper error handling here
			throw new Exception('Access only via AJAX request!');
		}
        
        // Retrieve address record
        $ajax = new \Tranquility\View\AjaxResponse();
        $response = $this->_service->find($id);
        if ($response->containsErrors()) {
            $ajax->addContent('process-message-container', $this->_renderPartial('administration._partials.errors', ['messages' => $response->getMessages()]), 'showElement', array('process-message-container'));
            return Response::json($ajax->toArray());
        }
        
        // Get existing details from address
        $address = $response->getFirstContentItem();
        switch (get_class($address)) {
            case 'Tranquility\Data\BusinessObjects\AddressPhysicalBusinessObject':
                $entityType = EnumEntityType::AddressPhysical;
                $type = EnumAddressType::Physical;
                break;
            case 'Tranquility\Data\BusinessObjects\AddressPhoneBusinessObject':
                $entityType = EnumEntityType::AddressPhone;
                $type = EnumAddressType::Phone;
                break;
            case 'Tranquility\Data\BusinessObjects\AddressElectronicBusinessObject':
                $entityType = EnumEntityType::AddressElectronic;
                $type = EnumAddressType::Electronic;
            default:
                $entityType = '';
                $type = '';
        }
        
        // Render dialog
        $data = array(
            'parentId' => $address->getParentEntity()->id,
            'type' => $entityType,
            'address' => $address
        );
        $dialog = $this->_renderPartial('administration.addresses._partials.dialogs.update-address-'.$type, $data);
        $ajax->addContent('modal-content', $dialog, 'displayDialog');
        return Response::json($ajax->toArray());
    }
    
    /**
	 * Store details of a new or updated address
	 *
	 * @return Response
	 */
	public function store(Request $request) {
		// Save details of address
		$params = $request->all();
		$id = $request->input('id', 0);
        $parentId = $request->input('parentId', 0);
		
		// Add in additional audit trail details
		$params['updateBy'] = Auth::user();
		$params['updateDateTime'] = Carbon::now();
		$params['transactionSource'] = EnumTransactionSource::UIBackend;
        
        // Retrieve parent entity details
        $response = $this->_service->findParentEntity($parentId);
        if ($response->containsErrors()) {
            $ajax->addContent('process-message-container', $this->_renderPartial('administration._partials.errors', ['messages' => $response->getMessages()]), 'showElement', array('process-message-container'));
            return Response::json($ajax->toArray());
        }
        $parentEntity = $response->getFirstContentItem();
        		
		// Create or update record		
		if ($id != 0) {
            // Update existing record
            $params['updateReason'] = 'backend address update';
			$response = $this->_service->update($id, $params);
		} else {
            // Create new address record
            $params['parent'] = $parentEntity;
            $params['updateReason'] = 'backend address create';
			$response = $this->_service->create($params);
		}
        
        // Set up response
        $ajax = new \Tranquility\View\AjaxResponse();
        if ($response->containsErrors()) {
			// Errors encountered - redisplay form with error messages
            $ajax->addContent('modal-dialog-container #process-message-container', $this->_renderPartial('administration._partials.errors', ['messages' => $response->getMessages()]), 'showElement', array('modal-dialog-container #process-message-container'));
			$ajax->addMessages($response->getMessages());
            return Response::json($ajax->toArray());
		}

        // Render address panel for person
        $address = $response->getFirstContentItem();
        $ajax->addContent('physical-addresses-container', $this->_renderPartial('administration.addresses._partials.panels.physical-address', ['addresses' => $parentEntity->getPhysicalAddresses(), 'parentId' => $parentEntity->id]), 'attachCommonHandlers');
        $ajax->addContent('process-message-container', $this->_renderPartial('administration._partials.errors', ['messages' => $response->getMessages()]), 'showElement', array('process-message-container'));
        $ajax->addCallback('closeDialog');
        return Response::json($ajax->toArray());
	}
    
    public function delete($id, Request $request) {
        // Save details of address
		$params = $request->all();
		$id = $request->input('id', 0);
        
        // Retrieve address record
        $ajax = new \Tranquility\View\AjaxResponse();
        $response = $this->_service->find($id);
        if ($response->containsErrors()) {
            $ajax->addContent('process-message-container', $this->_renderPartial('administration._partials.errors', ['messages' => $response->getMessages()]), 'showElement', array('process-message-container'));
            return Response::json($ajax->toArray());
        }
        $address = $response->getFirstContentItem();
        $parentEntity = $address->getParentEntity();
		
		// Add in additional audit trail details
		$params['updateBy'] = Auth::user();
		$params['updateReason'] = 'backend address delete';
		$params['updateDateTime'] = Carbon::now();
		$params['transactionSource'] = EnumTransactionSource::UIBackend;
        
		// Delete address record
        $ajax = new \Tranquility\View\AjaxResponse();
        $response = $this->_service->delete($id, $params);
        if ($response->containsErrors()) {
			// Errors encountered - redisplay form with error messages
            $ajax->addContent('process-message-container', $this->_renderPartial('administration._partials.errors', ['messages' => $response->getMessages()]), 'showElement', array('process-message-container'));
			$ajax->addMessages($response->getMessages());
            return Response::json($ajax->toArray());
		}

        // Render address panel for person
        $ajax->addContent('physical-addresses-container', $this->_renderPartial('administration.addresses._partials.panels.physical-address', ['addresses' => $parentEntity->getPhysicalAddresses(), 'parentId' => $person->id]), 'attachCommonHandlers');
        $ajax->addContent('process-message-container', $this->_renderPartial('administration._partials.errors', ['messages' => $response->getMessages()]), 'showElement', array('process-message-container'));
        $ajax->addCallback('closeDialog');
        return Response::json($ajax->toArray());
    }
}
