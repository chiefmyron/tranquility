<?php namespace Tranquility\Http\Controllers\Administration;

use \Exception as Exception;
use \Session as Session;
use \Response as Response;
use \Auth as Auth;
use Illuminate\Http\Request as Request;

use Carbon\Carbon;
use Tranquility\View\AjaxResponse                          as AjaxResponse;
use Tranquility\Services\AddressService                    as AddressService;
use Tranquility\Services\AddressPhysicalService            as AddressPhysicalService;
use Tranquility\Enums\BusinessObjects\Address\AddressTypes as EnumAddressType;
use Tranquility\Enums\System\EntityType                    as EnumEntityType;
use Tranquility\Enums\System\TransactionSource             as EnumTransactionSource;
use Tranquility\Enums\System\MessageLevel                  as EnumMessageLevel;

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
     * Address service used for the majority of address operations
     * @var \Tranquility\Services\AddressService
     */
	private $_addressService;
    
    /**
     * Address service used specifically for physical addresses
     * @var \Tranquility\Services\AddressPhysicalService
     */
	private $_physicalAddressService;
    
	/**
     * Constructor
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(AddressService $addressService, AddressPhysicalService $physicalAddressService) {
		$this->_addressService = $addressService;
        $this->_physicalAddressService = $physicalAddressService;
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
        $response = $this->_getService(EnumAddressType::Physical)->find($id);
        if ($response->containsErrors()) {
            $ajax->addMessages($result->getMessages());
            $ajax->addContent('#process-message-container', $this->_renderPartial('administration._partials.errors', ['messages' => $response->getMessages()]), 'core.showElement', array('process-message-container'));
            return $ajax;
        }
        
		// AJAX response
		$ajax = new \Tranquility\View\AjaxResponse();
		$dialog = $this->_renderPartial('administration.addresses._partials.dialogs.show-map', ['address' => $response->getFirstContentItem()]);
        $ajax->addContent('#modal-content', $dialog, 'core.displayDialog', [null, "large"]);
		return Response::json($ajax->toArray());
	}
    
    /**
     * Show form for adding a new address
     *
     * @param string  $category    Address type (physical, telephone, electronic)
     * @param Request $request
     * @return Response
     */
    public function create($category, Request $request) {
        // Ensure this is received as an ajax request only
		if (!$request->ajax()) {
			// TODO: Proper error handling here
			throw new Exception('Access only via AJAX request!');
		}
        
        // Setup data for view
        $ajax = new \Tranquility\View\AjaxResponse();
        if ($category == EnumAddressType::Physical) {
            // Physical address
            $data = array(
                'parentId' => $request->input('parentId', 0),
                'type' => EnumEntityType::AddressPhysical
            );
            $dialog = $this->_renderPartial('administration.addresses._partials.dialogs.create-address-physical', $data);
        } else {
            // Other address
            $data = array(
                'parentId' => $request->input('parentId', 0),
                'type' => EnumEntityType::Address,
                'category' => $category
            );
            $dialog = $this->_renderPartial('administration.addresses._partials.dialogs.create-address', $data);
        }
        $ajax->addContent('#modal-content', $dialog, 'core.displayDialog');
        return Response::json($ajax->toArray());
    }
    
    /**
     * Show form for updating an existing physical address
     *
     * @param string  $category    Address type (physical, telephone, electronic)
     * @param int     $id          Address entity ID
     * @return Response
     */
    public function update($category, $id, Request $request) {
        // Ensure this is received as an ajax request only
		if (!$request->ajax()) {
			// TODO: Proper error handling here
			throw new Exception('Access only via AJAX request!');
		}
        
        // Retrieve address record
        $ajax = new \Tranquility\View\AjaxResponse();
        $response = $this->_getService($category)->find($id);
        if ($response->containsErrors()) {
            $ajax->addContent('#process-message-container', $this->_renderPartial('administration._partials.errors', ['messages' => $response->getMessages()]), 'core.showElement', array('process-message-container'));
            return Response::json($ajax->toArray());
        }
        
        // Render dialog
        $address = $response->getFirstContentItem();
        $data = array('address' => $address);
        if ($address->getEntityType() == EnumEntityType::AddressPhysical) {
            $dialog = $this->_renderPartial('administration.addresses._partials.dialogs.update-address-physical', $data);    
        } else {
            $dialog = $this->_renderPartial('administration.addresses._partials.dialogs.update-address', $data);
        }
        
        $ajax->addContent('#modal-content', $dialog, 'core.displayDialog');
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
        $category = $request->input('category', '');
        $parentId = $request->input('parentId', 0);
		
		// Add in additional audit trail details
		$params['updateBy'] = Auth::user();
		$params['updateDateTime'] = Carbon::now();
		$params['transactionSource'] = EnumTransactionSource::UIBackend;
        
		// Create or update record		
		if ($id != 0) {
            // Update existing record
            $params['updateReason'] = 'backend address update';
			$response = $this->_getService($category)->update($id, $params);
		} else {
            // Create new address record
            $params['updateReason'] = 'backend address create';
			$response = $this->_getService($category)->create($params);
		}
        
        // Set up response
        $ajax = new \Tranquility\View\AjaxResponse();
        if ($response->containsErrors()) {
			// Errors encountered - redisplay form with error messages
            $ajax->addContent('#modal-dialog-container #process-message-container', $this->_renderPartial('administration._partials.errors', ['messages' => $response->getMessages()]), 'core.showElement', array('modal-dialog-container #process-message-container'));
			$ajax->addMessages($this->_renderInlineMessages($response->getMessages()));
            return Response::json($ajax->toArray());
		}

        // Render address panel for person
        $address = $response->getFirstContentItem();
        $ajax = $this->_refreshAddressList($parentId, $category);
        $ajax->addContent('#process-message-container', $this->_renderPartial('administration._partials.errors', ['messages' => $response->getMessages()]), 'core.showElement', array('process-message-container'));
        $ajax->addCallback('core.closeDialog');
        return Response::json($ajax->toArray());
	}
    
    /**
     * Confirm deletion of an address record
     *
     * @param $request Request
     * @return Response
     */
    public function confirm($category, $id, Request $request) {
		// Ensure this is received as an ajax request only
		if (!$request->ajax()) {
			// TODO: Proper error handling here
			throw new Exception('Access only via AJAX request!');
		}
        
		// AJAX response
        $dialog = $this->_renderPartial('administration.addresses._partials.dialogs.confirm-delete', ['id' => $id, 'type' => $category]);
		$ajax = new \Tranquility\View\AjaxResponse();
		$ajax->addContent('#modal-content', $dialog, 'core.displayDialog');
		return Response::json($ajax->toArray());
	}
    
    public function delete($type, $id, Request $request) {
        // Save details of address
		$params = $request->all();
        
        // Retrieve address record
        $ajax = new \Tranquility\View\AjaxResponse();
        $response = $this->_getService($type)->find($id);
        if ($response->containsErrors()) {
            $ajax->addContent('#process-message-container', $this->_renderPartial('administration._partials.errors', ['messages' => $response->getMessages()]), 'core.showElement', array('process-message-container'));
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
        $response = $this->_getService($type)->delete($id, $params);
        if ($response->containsErrors()) {
			// Errors encountered - redisplay form with error messages
            $ajax->addContent('#process-message-container', $this->_renderPartial('administration._partials.errors', ['messages' => $response->getMessages()]), 'core.showElement', array('process-message-container'));
			$ajax->addMessages($this->_renderInlineMessages($response->getMessages()));
            return Response::json($ajax->toArray());
		}
        
        // Render address panel for parent entity
        $ajax = $this->_refreshAddressList($parentEntity, $type);
        $ajax->addContent('#process-message-container', $this->_renderPartial('administration._partials.errors', ['messages' => $response->getMessages()]), 'core.showElement', array('process-message-container'));
        $ajax->addCallback('core.closeDialog');
        return Response::json($ajax->toArray());
    }
    
    public function makePrimary($type, $id, Request $request) {
        // Save details of address
		$params = $request->all();
        
        // Add in additional audit trail details
		$params['updateBy'] = Auth::user();
		$params['updateReason'] = 'backend change primary contact';
		$params['updateDateTime'] = Carbon::now();
		$params['transactionSource'] = EnumTransactionSource::UIBackend;
        
        // Cannot flag physical records as primary contact
        if ($type == EnumAddressType::Physical) {
            throw new Exception("Cannot set primary contact flag for physical addresses");
        } 
        
		// Update affected records
        $ajax = new \Tranquility\View\AjaxResponse();
        $response = $this->_getService($type)->makePrimary($id, $params);
        if ($response->containsErrors()) {
			// Errors encountered - redisplay form with error messages
            $ajax->addContent('#process-message-container', $this->_renderPartial('administration._partials.errors', ['messages' => $response->getMessages()]), 'core.showElement', array('process-message-container'));
			$ajax->addMessages($this->_renderInlineMessages($response->getMessages()));
            return Response::json($ajax->toArray());
		}
        
        // Render address panel for parent entity
        $address = $response->getFirstContentItem();
        $ajax = $this->_refreshAddressList($address->getParentEntity(), $type);
        $ajax->addContent('#process-message-container', $this->_renderPartial('administration._partials.errors', ['messages' => $response->getMessages()]), 'core.showElement', array('process-message-container'));
        $ajax->addCallback('core.closeDialog');
        return Response::json($ajax->toArray());
    }
    
    private function _getService($type) {
        if ($type == EnumAddressType::Physical) {
            return $this->_physicalAddressService;
        } else {
            return $this->_addressService;
        }
    }
    
    private function _refreshAddressList($parent, $type) {
        if (is_numeric($parent)) {
            // Retrieve parent entity details
            $response = $this->_getService($type)->findEntity($parent);
            if ($response->containsErrors()) {
                $ajax->addContent('#process-message-container', $this->_renderPartial('administration._partials.errors', ['messages' => $response->getMessages()]), 'core.showElement', array('process-message-container'));
                return $ajax;
            }
            $parent = $response->getFirstContentItem();
        }
        
        // Render refreshed address list panel
        $ajax = new \Tranquility\View\AjaxResponse();
        $ajax->addContent('#profile-primary-addresses', $this->_renderPartial('administration.addresses._partials.panels.profile-primary-address', ['entity' => $parent]));
        $ajax->addContent("#".$type.'-addresses-container', $this->_renderPartial('administration.addresses._partials.panels.'.$type.'-address', ['addresses' => $parent->getAddresses($type), 'parentId' => $parent->id]));
        return $ajax;
    }
}
