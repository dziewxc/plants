<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/hallo/{name}/{day}", name="homepage")
     */
    public function indexAction($name, $day)
    {
        return $this->render('default/hello.html.twig', array(
			'name' => $name,
			'day' => $day
		));
    }
	
	/**
	* @Route("/article/{topic}", name="article")
	*/
	
	public function articleAction($topic)
	{
		return $this->render('default/article.html.twig', array(
			"topic" => $topic
		));
	}
	
	/**
	* @Route("/category/{category}", name="category")
	*/
	
	public function categoriesAction($category)
	{
		return $this->render('default/category.html.twig', array(
			"category" => $category
		));
	}
	
	/**
	* @Route("/users/{user}.{_format}", defaults={"_format" = "html"}, requirements={"_format"="html|xml|rdf"}, name="users")
	*/
	
	public function usersAction($user, $_format)
	{
		return $this->render('default/users.' . $_format . '.twig', array(
			"user" => $user
		));
	}
	
	/**
	* @Route("/blog/{topic}", name="blog")
	*/
	public function blogAction($topic)
	{
		return $this->redirectToRoute('article', array(
			'topic' => $topic
		));
		//throw $this->createNotFoundException();  //nie wiem co ma się tu wydarzyć
		//throw new \Exception("wrong");
	}
	
	/**
	* @Route("/request", name="request")
	*/
	
	 public function requestAction(Request $request)
    {
        // is it an Ajax request?
        $isAjax = $request->isXmlHttpRequest();

        // what's the preferred language of the user?
        $language = $request->getPreferredLanguage(array('en', 'fr'));

        // get the value of a $_GET parameter
        $pageName = $request->query->get('page');

        // get the value of a $_POST parameter
        $pageName = $request->request->get('page');
		
		return $this->render('default/hello.html.twig', array(
			'name' => $language,
			'day' => $isAjax
		));
    }
	
	
	/**
	* @Route("/info", name="info")
	*/
	public function infoAction(Request $request)
	{
		$session = $request->getSession();
		
		//store an attribute for reuse during the later user request
		$session->set('foo', 'bar');
		
		//get the value of a session attribute
		$session->get('foo');
		
		return $this->render('default/hello.html.twig', array(
			'name' => $session->get('foo'),
			'day' => 'nana'
		));	
	}
    public function lolAction()
    {
        echo "dupa";
        return Response();
    }
}












