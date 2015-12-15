<?php

namespace SE\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{
    public function indexAction()
    {
        return $this->render('SEAdminBundle:Admin:index.html.twig', array(
                // ...
            ));    
    }

    public function menuAction()
	{
		return $this->render('SEAdminBundle:Admin:menu.html.twig');
	}

}
