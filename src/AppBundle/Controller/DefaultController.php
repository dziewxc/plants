<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
}












