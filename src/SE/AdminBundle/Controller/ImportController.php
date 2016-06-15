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

    $consoleNB=0;

    $deleteImport = $em->getRepository('SEInputBundle:SapImports')->findOneBy(array('id' => $idImport));
    if($deleteImport){
        $dateImport = $deleteImport->getDate();
        //look for duplicate
        $duplicateImport = $em->getRepository('SEInputBundle:SapImports')->findBy(array('date' => $dateImport));
        $allSAProws = $em->getRepository('SEInputBundle:SAPRF')->getDayLines($dateImport);

        if(sizeof($duplicateImport)>1){
            //find duplicaterows in saprf and delete them
            foreach ($allSAProws as $SapRow) {
                foreach ($allSAProws as $duplicateSearch) {
                    if($SapRow->getTransferOrder() == $duplicateSearch->getTransferOrder() &&
                        $SapRow->getMaterial() == $duplicateSearch->getMaterial() &&
                        $SapRow->getDateConfirmation() == $duplicateSearch->getDateConfirmation() &&
                        $SapRow->getTimeConfirmation() == $duplicateSearch->getTimeConfirmation() &&
                        $SapRow->getUser() == $duplicateSearch->getUser() &&
                        $SapRow->getSourceStorageBin() == $duplicateSearch->getSourceStorageBin() &&
                        $SapRow->getDestinationStorageType() == $duplicateSearch->getDestinationStorageType() &&
                        $SapRow->getId() != $duplicateSearch->getId()){

                        $consoleNB+=1;
 //                       $em->remove($duplicateSearch);
                    }
                }    
            }

            $response = array("code" => 100, "success" => true, "comment" => "Duplicate(s) found : number = ".sizeof($duplicateImport)." - ".$consoleNB." rows deleted");
        }else{
            foreach ($allSAProws as $SapRow) {
                $consoleNB+=1;
 //               $em->remove($SapRow);  
            }
            $response = array("code" => 100, "success" => true, "comment" => "No duplicate".sizeof($allSAProws)." found - ".$consoleNB." rows deleted");
        }
/*
        $resetInputs = $em->getRepository('SEInputBundle:UserInput')->findBy(array('date_input') => $dateImport));
        foreach ($resetInputs as $i) {
            $i->setProcess(0);   
        }
/*




      $deleteSAPImport = $em->getRepository('SEInputBundle:SA')->findBy(array('user_input' => $idInput));
      if($deleteEntries){
        foreach ($deleteEntries as $deleteEntry) {
          $deleteActivityHours = $em->getRepository('SEInputBundle:ActivityHours')->findBy(array('input' => $deleteEntry));
          if ($deleteActivityHours) {
            foreach ($deleteActivityHours as $deleteActivityHour) {
              // un-record to lines too
              foreach ($em->getRepository('SEInputBundle:SAPRF')->getRecordedTo($deleteInput->getDateInput(), $deleteEntry->getSesa()) as $recordedTo) {
                $recordedTo->setRecorded(0);
              }
              $em->remove($deleteActivityHour);
            }
          }
          $em->remove($deleteEntry);
        }
      }
      
      //reset manual TO Lines

      $manualTOlines = $em->getRepository('SEInputBundle:SAPRF')->resetGeneralManualTo($deleteInput->getDateInput(), $deleteInput->getTeam()->getId());
      //time definition
      $otStart = $deleteInput->getOtStartTime();
      $otEnd = $deleteInput->getOtEndTime();
      $start = $deleteInput->getShift()->getStartTime();
      $end = $deleteInput->getShift()->getEndTime();
      $regularReverse = ($start < $end ? true : false);
      $otReverse = ($otStart > $otEnd ? true : false);

      if($manualTOlines){
        foreach ($manualTOlines as $manualToLine) {
          $timeConf = $manualToLine->getTimeConfirmation(); 
          //if inside right time interval + to line not already affected
          if($manualToLine->getRecorded() == 1){ 
            if( ( $regularReverse and ($timeConf <= $end) and ($timeConf >= $start) ) or ( !$regularReverse and ( ( $timeConf >= $start ) or ( $timeConf <= $end ) ) ) ) { //regular hours
              $manualToLine->setRecorded(null);  
            }
          }
        }
      }

      $em->remove($deleteInput);
      $em->flush();

      $response = array("code" => 100, "success" => true);
*/
    }else{
      $response = array("code" => 400, "success" => false);
    }

    return new Response(json_encode($response)); 
    }
}