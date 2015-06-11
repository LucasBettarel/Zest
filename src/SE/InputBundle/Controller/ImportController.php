<?php

namespace SE\InputBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use SE\InputBundle\Extensions\SapConnection;
use SE\InputBundle\Entity\SAPRF;


class EntryController extends Controller
{
	public function sapAction()
	{
    $connection = new SapConnection();
    $connection->setUp()->sapConnect();

    $em = $this->getDoctrine()->getManager();

    $res = $connection->readTable();
    $connection->saveTable($res, $em);
    $connection->sapClose();

    return $this->render('SEInputBundle:Import:sap_import.html.twig');
	}

	public function menuAction()
  	{
    	return $this->render('SEInputBundle:Entry:menu.html.twig');
  	}
}
