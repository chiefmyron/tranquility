<?php namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request as Request;

use Tranquility\Enums\System\EntityType as EnumEntityType;

// Entity services
use Tranquility\Services\PersonService  as PersonService;
use Tranquility\Services\AccountService as AccountService;
use Tranquility\Services\TagService     as TagService;

class SearchController extends Controller {

	/** Holds list of entity services used during search */
	protected $_entityServices = array();

	/** Service used for tag searches */
	protected $_tagService;

	/** The set of searchable entities */
	protected $_searchableEntities = array(
		EnumEntityType::Person,
		EnumEntityType::Account
	);

	/*
	|--------------------------------------------------------------------------
	| Search Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders the search and search results pages for the
	| backend application
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(AccountService $account, PersonService $person, TagService $tag) {
		// Entity services
		$this->_services = array(
			EnumEntityType::Person => $person,
			EnumEntityType::Account => $account,
		);

		// Metadata services
		$this->_tagService = $tag;
	}

	/**
	 * Show the application welcome screen to the user.
	 *
	 * @return Response
	 */
	public function index(Request $request) {
		// Get search term
		$searchTerm = trim($request->get('q', ''));
		

		// If no search term is provided, start with the default page
		if ($searchTerm == '') {
			$query = array('searchTerm' => $searchTerm);
			return view('administration.search.index', ['query' => $query]);
		}

		// Get any additional search parameters
		$pageNumber = $request->get('page', 1);
        $recordsPerPage = $request->get('recordsPerPage', 20);

		$searchResults = array();
		foreach ($this->_searchableEntities as $entityType) {
			$response = $this->_services[$entityType]->search($searchTerm, $recordsPerPage, $pageNumber);
			$searchResults[$entityType] = $response->getContent();
		}

		// Display results
		return view('administration.search.results', ['results' => $searchResults]);
	}

}
