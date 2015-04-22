<?php

namespace SE\ZestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class AdvertController extends Controller
{
	public function indexAction()
	{
		$content = $this
            ->get('templating')
            ->render('SEZestBundle:Advert:index.html.twig', array(
                'nom' => 'Bite'
            )
        );
		return new Response($content);
    }
}