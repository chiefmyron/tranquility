<?php namespace App\Http\Controllers;

use Doctrine\ORM\EntityManagerInterface;

class TestController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Welcome Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders the "marketing page" for the application and
	| is configured to only allow guests. Like most of the other sample
	| controllers, you are free to modify or remove it as you desire.
	|
	*/

	protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

	/**
	 * Show the application welcome screen to the user.
	 *
	 * @return Response
	 */
	public function index()
	{
        $entity = $this->em->find('\Tranquility\Data\BusinessObjects\User', 4);
        //echo $entity->getFullName();
        //var_dump($entity->toArray());
        //var_dump($entity->getAuditTrailDetails());
        echo 'Remember token: '.$entity->getRememberToken();
		//return view('welcome');
	}
	
	public function administration() {
		echo 'This is the administration section!';
	}

}
