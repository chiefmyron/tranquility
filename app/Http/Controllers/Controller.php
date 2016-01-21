<?php namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;

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

}
