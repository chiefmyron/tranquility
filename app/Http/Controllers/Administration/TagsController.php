<?php namespace App\Http\Controllers\Administration;

use \Exception as Exception;
use \Session as Session;
use \Response as Response;
use \Auth as Auth;
use Illuminate\Http\Request as Request;
use App\Http\Controllers\Controller;

use Carbon\Carbon;
use Tranquility\View\AjaxResponse                          as AjaxResponse;
use Tranquility\Services\TagService                        as TagService;

use Tranquility\Enums\System\EntityType                    as EnumEntityType;
use Tranquility\Enums\System\TransactionSource             as EnumTransactionSource;
use Tranquility\Enums\System\MessageLevel                  as EnumMessageLevel;

class TagsController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Tags controller
	|--------------------------------------------------------------------------
	|
	| This controller renders the interface for adding and maintaining tags
    | associated with other business objects
	|
	*/
	
    /**
     * Service used to maintain tags
     * @var \Tranquility\Services\TagService
     */
	private $_service;
    
	/**
     * Constructor
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(TagService $service) {
		$this->_service = $service;
	}

	/**
	 * Display a dialog containing a Google map iframe
	 *
	 * @return Response
	 */
	public function update($parentId, Request $request) {
        // Ensure this is received as an ajax request only
		if (!$request->ajax()) {
			// TODO: Proper error handling here
			throw new Exception('Access only via AJAX request!');
		}
        
        // Retrieve parent entity
        $response = $this->_service->findParentEntity($parentId);
        if ($response->containsErrors()) {
            $ajax->addMessages($result->getMessages());
            $ajax->addContent('process-message-container', $this->_renderPartial('administration._partials.errors', ['messages' => $response->getMessages()]), 'showElement', array('process-message-container'));
            return $ajax;
        }
        $entity = $response->getFirstContentItem();
        $tags = $entity->getTags();
        $entityTypes = array($entity->getEntityType());
        if ($entity->getEntityType() == EnumEntityType::Person && $entity->getUserAccount() != null) {
            $entityTypes[] = EnumEntityType::User;
        }
        
		// AJAX response
		$ajax = new \Tranquility\View\AjaxResponse();
		$dialog = $this->_renderPartial('administration.tags._partials.dialogs.update', ['tags' => $tags, 'entityTypes' => $entityTypes]);
        $ajax->addContent('modal-content', $dialog, 'displayDialog', [null, "large"]);
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
        
        // Retrieve parent entity details
        $response = $this->_getService($category)->findParentEntity($parentId);
        if ($response->containsErrors()) {
            $ajax->addContent('process-message-container', $this->_renderPartial('administration._partials.errors', ['messages' => $response->getMessages()]), 'showElement', array('process-message-container'));
            return Response::json($ajax->toArray());
        }
        $parentEntity = $response->getFirstContentItem();
        		
		// Create or update record		
		if ($id != 0) {
            // Update existing record
            $params['updateReason'] = 'backend address update';
			$response = $this->_getService($category)->update($id, $params);
		} else {
            // Create new address record
            $params['parent'] = $parentEntity;
            $params['updateReason'] = 'backend address create';
			$response = $this->_getService($category)->create($params);
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
        $ajax = $this->_refreshAddressList($parentEntity, $category);
        $ajax->addContent('process-message-container', $this->_renderPartial('administration._partials.errors', ['messages' => $response->getMessages()]), 'showElement', array('process-message-container'));
        $ajax->addCallback('closeDialog');
        return Response::json($ajax->toArray());
	}
    
    
    public function delete($type, $id, Request $request) {
        // Save details of address
		$params = $request->all();
        
        // Retrieve address record
        $ajax = new \Tranquility\View\AjaxResponse();
        $response = $this->_getService($type)->find($id);
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
        $response = $this->_getService($type)->delete($id, $params);
        if ($response->containsErrors()) {
			// Errors encountered - redisplay form with error messages
            $ajax->addContent('process-message-container', $this->_renderPartial('administration._partials.errors', ['messages' => $response->getMessages()]), 'showElement', array('process-message-container'));
			$ajax->addMessages($response->getMessages());
            return Response::json($ajax->toArray());
		}
        
        // Render address panel for parent entity
        $ajax = $this->_refreshAddressList($parentEntity, $type);
        $ajax->addContent('process-message-container', $this->_renderPartial('administration._partials.errors', ['messages' => $response->getMessages()]), 'showElement', array('process-message-container'));
        $ajax->addCallback('closeDialog');
        return Response::json($ajax->toArray());
    }
  
}
