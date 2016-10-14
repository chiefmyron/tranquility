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
		$queryString = trim($request->get('q', ''));

		// If no search term is provided, start with the default page
		if ($queryString == '') {
			$searchParams = array('queryString' => $queryString);
			return view('administration.search.index', ['searchParams' => $searchParams]);
		}

		// Get search terms from string (@see http://stackoverflow.com/questions/7943424/parse-search-string-for-phrases-and-keywords)
		preg_match_all('~(?|"([^"]+)"|(\S+))~', $queryString, $matches);
		$searchTerms = $matches[1];

		// Get any additional search parameters
		$orderConditions = array();
		$pageNumber = $request->get('page', 1);
        $recordsPerPage = $request->get('recordsPerPage', 20);

		$searchResults = array();
		$totalResults = 0;
		foreach ($this->_searchableEntities as $entityType) {
			$response = $this->_services[$entityType]->search($searchTerms, $orderConditions, $recordsPerPage, $pageNumber);
			$searchResults[$entityType] = $response->getContent();
			$totalResults = $totalResults + count($searchResults[$entityType]);
		}

		// Display results
		return view('administration.search.results', ['searchParams' => array('queryString' => $queryString), 'results' => $searchResults, 'totalResults' => $totalResults]);
	}

}
