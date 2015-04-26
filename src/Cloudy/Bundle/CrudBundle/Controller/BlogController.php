<?php
// src/Cloudy/Bundle/CrudBundle/Controller/BlogController.php

namespace Cloudy\Bundle\CrudBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BlogController extends Controller
{
	public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $blog = $em->getRepository('CloudyCrudBundle:Blog')->find($id);

        if (!$blog) {
            throw $this->createNotFoundException('Cześć, niestety nie posiadamy takiego wpisu');
        }
		
		$comments = $em->getRepository('CloudyCrudBundle:Comment')
					   ->getCommentsForBlog($blog->getId());

        return $this->render('CloudyCrudBundle:Blog:show.html.twig', array(
            'blog'      => $blog,
			'comments'  => $comments,
        ));
    }

}
