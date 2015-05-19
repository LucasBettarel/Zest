<?php

namespace SE\InputBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class EntryController extends Controller
{
	public function indexAction()
	{
		return $this->render('SEInputBundle:Entry:index.html.twig');
	}

	public function menuAction()
  	{
    	return $this->render('SEInputBundle:Entry:menu.html.twig');
  	}
}
