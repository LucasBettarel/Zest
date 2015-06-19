<?php

namespace SE\InputBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use SE\InputBundle\Extensions\SapConnection;
use SE\InputBundle\Entity\SAPRF;
use SE\InputBundle\Entity\SapImports;


class ImportController extends Controller
{
	public function sapAction()
	{
    $connection = new SapConnection();
   // $connection->setUp();
    $connection->sapConnect();

    $em = $this->getDoctrine()->getManager();

    $res = $connection->readTable();
    $connection->saveTable($res, $em);
    $connection->sapClose();

    $listImport = $this->getDoctrine()
        ->getManager()
        ->getRepository('SEInputBundle:SAPRF')
        ->findAll()
      ;

    return $this->render('SEInputBundle:Import:sap_import.html.twig', array(
        'listImport' => $listImport
        ));
	}

    public function setAction()
    {
        $listSapImport = $this->getDoctrine()
        ->getManager()
        ->getRepository('SEInputBundle:SapImports')
        ->findAll()
      ;

        return $this->render('SEInputBundle:Import:set_import.html.twig', array(
            'listSapImport' => $listSapImport
            ));
    }
}