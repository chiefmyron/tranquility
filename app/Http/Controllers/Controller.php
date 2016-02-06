<?php namespace App\Http\Controllers;

use \Session;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;

use Tranquility\Enums\System\MessageLevel as EnumMessageLevel;

abstract class Controller extends BaseController {

	use DispatchesJobs, ValidatesRequests;
	
	/** 
	 * Render a partial view without any other surrounding HTML. Useful
	 * for generating HTML to return in an AJAX response
	 *
	 * @param string $partialPath  Path of the view to render
	 * @param array  $data         Data values to pass into the view
	 * @return string              Rendered partial view
	 */
	protected function _renderPartial($partialPath, $data = array()) {
		$params = array(
			'partialPath' => $partialPath,
			'data' => $data	
		);
		return view('administration.ajax', $params)->render();
	}
    
    protected function _addProcessMessage($level, $text, $params = array()) {
        if (!EnumMessageLevel::isValidValue($level)) {
            throw new \Exception('Message level "'.$level.'" is not valid');
        }
        
        // Format message
        $message = array(
            'text' => $text,
            'level' => $level
        );
        if (is_array($params) && count($params) > 0) {
            $message['params'] = $params;
        }
        
        // Add message to those already stored in session
        $messages = Session::pull('messages');
        $messages[] = array('text' => $text, 'level' => $level);
        Session::flash('messages', $messages);
    }

}
