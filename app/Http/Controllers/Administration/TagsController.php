<?php namespace App\Http\Controllers\Administration;

use \Exception as Exception;
use \Session as Session;
use \Response as Response;
use \Auth as Auth;
use Illuminate\Http\Request as Request;
use App\Http\Controllers\Administration\Controller;

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
	 * Display a dialog to allow tags to be added or removed for a parent entity
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
            $ajax->addContent('#process-message-container', $this->_renderPartial('administration._partials.errors', ['messages' => $response->getMessages()]), 'core.showElement', array('process-message-container'));
            return $ajax;
        }
        $entity = $response->getFirstContentItem();
        $tags = $entity->getTags();
        
		// AJAX response
		$ajax = new \Tranquility\View\AjaxResponse();
		$dialog = $this->_renderPartial('administration.tags._partials.dialogs.update', ['tags' => $tags, 'parentId' => $parentId]);
        $ajax->addContent('#modal-content', $dialog, 'core.displayDialog', [null, "large"]);
		return Response::json($ajax->toArray());
	}
    
    /**
	 * Store updated list of tags
	 *
	 * @return Response
	 */
	public function store(Request $request) {
        // Ensure this is received as an ajax request only
		if (!$request->ajax()) {
			// TODO: Proper error handling here
			throw new Exception('Access only via AJAX request!');
		}
        
		// Save details of address
		$params = $request->all();
        $parentId = $request->input('parentId', 0);
        $tagString = $request->input('tags', null);
        
        // Extract individual tags
        $tags = array();
        if (!is_null($tagString) && $tagString !== "") {
            $tags = explode(',', $tagString);
            $tags = array_map('trim', $tags);
        }
		
        // Set tags for the parent entity
        $ajax = new \Tranquility\View\AjaxResponse();
        $response = $this->_service->setEntityTags($parentId, $tags);
        if ($response->containsErrors()) {
			// Errors encountered - redisplay form with error messages
            $ajax->addContent('#modal-dialog-container #process-message-container', $this->_renderPartial('administration._partials.errors', ['messages' => $response->getMessages()]), 'core.showElement', array('modal-dialog-container #process-message-container'));
			$ajax->addMessages($this->_renderInlineMessages($response->getMessages()));
            return Response::json($ajax->toArray());
		}

        // Render updated tag list
        $entity = $response->getFirstContentItem();
        $ajax->addContent('#tag-container', $this->_renderPartial('administration.tags._partials.panels.entity-tag-list', ['entity' => $entity, 'tags' => $entity->getTags()]));
        $ajax->addCallback('core.closeDialog');
        return Response::json($ajax->toArray());
	}
    
    /**
     * Remove a tag from the specified entity
     * Note: Does not delete the tag entirely - just the association with the entity
     *
     * @return Response
     */
    public function remove($parentId, $id, Request $request) {
        // Ensure this is received as an ajax request only
		if (!$request->ajax()) {
			// TODO: Proper error handling here
			throw new Exception('Access only via AJAX request!');
		}
        
        // Remove tag from entity
        $ajax = new \Tranquility\View\AjaxResponse();
        $response = $this->_service->removeTag($parentId, $id);
        if ($response->containsErrors()) {
			// Errors encountered - redisplay form with error messages
            $ajax->addContent('#process-message-container', $this->_renderPartial('administration._partials.errors', ['messages' => $response->getMessages()]), 'core.showElement', array('process-message-container'));
			$ajax->addMessages($this->_renderInlineMessages($response->getMessages()));
            return Response::json($ajax->toArray());
		}

        // Render updated tag list
        $entity = $response->getFirstContentItem();
        $ajax->addContent('#tag-container', $this->_renderPartial('administration.tags._partials.panels.entity-tag-list', ['entity' => $entity, 'tags' => $entity->getTags()]));
        $ajax->addCallback('core.closeDialog');
        return Response::json($ajax->toArray());
    }
    
    public function autocomplete(Request $request) {
        // Get the search term
        $term = $request->get('term', '');
        
        // Get the set of tags
        $filter = array(
            ['text', 'LIKE', $term]
        );
        $response = $this->_service->all($filter);
        if ($response->containsErrors()) {
			// Errors encountered - redisplay form with error messages
            $ajax->addContent('#process-message-container', $this->_renderPartial('administration._partials.errors', ['messages' => $response->getMessages()]), 'core.showElement', array('process-message-container'));
			$ajax->addMessages($this->_renderInlineMessages($response->getMessages()));
            return Response::json($ajax->toArray());
		}
        
        $tags = $response->getContent();
        $output = array();
        foreach ($tags as $tag) {
            $output[] = $tag->text;
        }
        
        echo json_encode($output);
    }
  
}
