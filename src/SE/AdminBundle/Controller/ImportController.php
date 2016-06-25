<?php

namespace SE\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use SE\InputBundle\Entity\SAPRF;
use SE\InputBundle\Entity\SapImports;
use Symfony\Component\HttpFoundation\Response;


class ImportController extends Controller
{
	public function sapnwrfcAction()
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

    return $this->render('SEAdminBundle:Import:sapnwrfc_import.html.twig', array(
        'listImport' => $listImport
        ));
	}

    public function sapAction()
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

        return $this->render('SEAdminBundle:Import:sap_import.html.twig', array(
            'listSapImport' => $listSapImport
            ));
    }

    public function deleteAction()
    { 
    $em = $this->getDoctrine()->getManager();
    $request = $this->get('request');        
    $idImport = $request->get('idImport');

    $rowsNB=0;
    $importsNB=0;
    $inputsNB=0;
    $entriesNB=0;

    $deleteImport = $em->getRepository('SEInputBundle:SapImports')->findOneBy(array('id' => $idImport));
    if($deleteImport){
        $dateImport = $deleteImport->getDate();
        
        // if duplicate import, take all of them
        $duplicateImport = $em->getRepository('SEInputBundle:SapImports')->findBy(array('date' => $dateImport));
        $allSAProws = $em->getRepository('SEInputBundle:SAPRF')->getDayLines($dateImport);
        
        //delete all that shit
        foreach ($allSAProws as $r) {
            $rowsNB+=1;
            $em->remove($r);
        }
        foreach ($duplicateImport as $d) {
            $importsNB+=1;
            $em->remove($d);
        }
        $resetInputs = $em->getRepository('SEInputBundle:UserInput')->findBy(array('dateInput' => $dateImport));
        foreach ($resetInputs as $i) {
            $i->setTotalToInput(0);
            $i->setManualTo(0);
            $i->setAutoTo(0);
            $i->setProcess(0);
            $inputsNB+=1;
            foreach ($i->getInputEntries() as $e) {
                $e->setTotalTo(0);  
                $entriesNB+=1;
            }
        }

        $em->flush();
        
        $response = array("code" => 100, "success" => true, "comment" => "Import(s) deleted: ".$importsNB." - SAP Lines deleted: ".$rowsNB." - Inputs resetted: ".$inputsNB." - Entries resetted: ".$entriesNB);
        
    }else{
      $response = array("code" => 400, "success" => false, "comment" => "Import not found");
    }

    return new Response(json_encode($response)); 
    }
}