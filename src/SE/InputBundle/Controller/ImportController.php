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
        $em = $this->getDoctrine()->getManager();

        $listSapImport = $em->getRepository('SEInputBundle:SapImports')->getAll();
        $importErrors = $em->getRepository('SEInputBundle:InputReview')->getLastMonthImportErrors();

        foreach ($listSapImport as $import) {
            foreach ($importErrors as $error) {
                if ( $import->getDate()->format("Y-m-d") == $error->getDate()->format("Y-m-d")){
                    $error->setStatus(1);
                }
            }
        }
        $em->flush();

        return $this->render('SEInputBundle:Import:set_import.html.twig', array(
            'listSapImport' => $listSapImport
            ));
    }
}