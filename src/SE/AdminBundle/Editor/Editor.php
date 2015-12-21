<?php

namespace SE\AdminBundle\Editor;

use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use SE\InputBundle\Entity\InputEntry;
use SE\InputBundle\Entity\ActivityHours;

class Editor
{
	protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function addRequest($request){
    	$em = $this->em;

    	if($request->getUserInput()){

    		//test if employee already exist in input
    		foreach ($request->getUserInput()->getInputEntries() as $i) {
    			if($i->getEmployee() == $request->getEmployee()){
    				return $res = array("code" => 400, "success" => false, "message" => "Employee already exists in Input");
    			}
    		}

    		$entry = new InputEntry();
    		$entry->setUserInput($request->getUserInput());
    		$entry->setEmployee($request->getEmployee());
    		$entry->setSesa($request->getSesa());
    		$entry->setPresent($request->getPresent());
    		$entry->setAbsenceReason($request->getAbsenceReason());
    		$entry->setComments($request->getComments());
    		$entry->setTotalHours(0);
    		$entry->setTotalTo(0);
    		$entry->setTotalProd(0);
    		$entry->setTotalWorkingHours(0);
    		$entry->setTotalOvertime(0);
    		$entry->setEditorStatus(null);

    		//persist first so it can have an identity to add activity hours
    		$em->persist($entry);
    		$em->flush();

    		$request->setInputEntry($entry);

    		//create Avtivities Hours
    		foreach ($request->getEditorActivities() as $eA) {
    			$aH = new ActivityHours();
    			$aH->setActivity($eA->getActivity());
    			$aH->setInput($entry);
    			$aH->setRegularHours($eA->getRegularHours());
    			$aH->setOtHours($eA->getOtHours());

    			$em->persist($aH);
    		}
    		$request->getUserInput()->setProcess(0);
    		$em->flush();

    		$res = array("code" => 100, "success" => true, "message" => "Entry added to Input");

    	}else{$res = array("code" => 400, "success" => false, "message" => "Input was not found");}

    	return $res;
    }

    public function editRequest($request){
    	$em = $this->em;

    	if($request->getInputEntry()){

    		$editEntry = $em->getRepository('SEInputBundle:InputEntry')->findOneBy(array('id' => $request->getInputEntry()->getId()));
		    if($editEntry){

		    	//delete current Activities Hours
			    $deleteActivityHours = $em->getRepository('SEInputBundle:ActivityHours')->findBy(array('input' => $editEntry));
			    if ($deleteActivityHours) {
			    	foreach ($deleteActivityHours as $deleteActivityHour) {
			        	$em->remove($deleteActivityHour);
			       	}
			    }

				// un-record to lines too
	          	foreach ($em->getRepository('SEInputBundle:SAPRF')->getRecordedTo($editEntry->getUserInput()->getDateInput(), $editEntry->getSesa()) as $recordedTo) {
		        	$recordedTo->setRecorded(0);
		        }

		        if($editEntry->getEmployee() != $request->getEmployee()){$editEntry->setEmployee($request->getEmployee());}
				if($editEntry->getSesa() != $request->getSesa()){$editEntry->setSesa($request->getSesa());}
				if($editEntry->getPresent() != $request->getPresent()){$editEntry->setPresent($request->getPresent());}
				if($editEntry->getAbsenceReason() != $request->getAbsenceReason()){$editEntry->setAbsenceReason($request->getAbsenceReason());}
				if($editEntry->getComments() != $request->getComments()){$editEntry->setComments($request->getComments());}
				$editEntry->setTotalHours(0);
	    		$editEntry->setTotalTo(0);
	    		$editEntry->setTotalProd(0);
	    		$editEntry->setTotalWorkingHours(0);
	    		$editEntry->setTotalOvertime(0);

	    		//create Avtivities Hours
	    		foreach ($request->getEditorActivities() as $eA) {
	    			$aH = new ActivityHours();
	    			$aH->setActivity($eA->getActivity());
	    			$aH->setInput($editEntry);
	    			$aH->setRegularHours($eA->getRegularHours());
	    			$aH->setOtHours($eA->getOtHours());

	    			$em->persist($aH);
	    		}
	    		$request->getUserInput()->setProcess(0);
	    		$em->flush();

	    		$res = array("code" => 100, "success" => true, "message" => "Entry edited, activity Hours updated and TO Lines reseted");
	    	}else{$res = array("code" => 400, "success" => false, "message" => "Entry was not found");}
    	}else{$res = array("code" => 400, "success" => false, "message" => "Entry was not found");}

    	return $res;
    }

	public function deleteRequest($request)
	{ 
		$em = $this->em;

		if($request->getInputEntry()){

			$deleteEntry = $em->getRepository('SEInputBundle:InputEntry')->findOneBy(array('id' => $editor->getInputEntry()->getId()));
		    if($deleteEntry){

		      $deleteActivityHours = $em->getRepository('SEInputBundle:ActivityHours')->findBy(array('input' => $deleteEntry));
		      if ($deleteActivityHours) {

		      	foreach ($deleteActivityHours as $deleteActivityHour) {
		          $em->remove($deleteActivityHour);
		        }
		      }

	      	  // un-record to lines too
	          foreach ($em->getRepository('SEInputBundle:SAPRF')->getRecordedTo($deleteEntry->getUserInput()->getDateInput(), $deleteEntry->getSesa()) as $recordedTo) {
	            $recordedTo->setRecorded(0);
	          }

	          //reset User Input
		      $deleteEntry->getUserInput()->setProcess(0);

		      //delete entry
		      $em->remove($deleteEntry);
		      $em->flush();

		      $res = array('code' => 100, "success" => true, "message" => "Entry deleted, TO Lines resetted");
		    }else{$res = array("code" => 400, "success" => false, "message" => "Entry to delete was not found");}  
		}else{$res = array("code" => 400, "success" => false, "message" => "Entry to delete was not found");}

		return $res;
	}

	public function editStatus($request, $statusId){
		$em = $this->em;
		$status = $em->getRepository('SEInputBundle:EditorStatus')->find($statusId);
	    
	   	if($request){
	   		$editor = $em->getRepository('SEInputBundle:EditorEntry')->find($request);
	   		if($editor){
	   			$editor->setEditorStatus($status);
	   			if($editor->getInputEntry()){
	   				$entry = $em->getRepository('SEInputBundle:InputEntry')->find($editor->getInputEntry()->getId());
	   				if($entry){
	   					$entry->setEditorStatus($status);
	   					$res = array("code" => 100, "success" => true, "message" => "Request & Entry status updated");
	   				}else{
	   					$res = array("code" => 400, "success" => false, "message" => "Request status updated, Entry was not found");
	   				}
		   		}elseif($editor->getEditorType()->getId() != 1){
		   			$res = array("code" => 400, "success" => false, "message" => "Request status updated, Entry was not found");
	   			}else{
		   			$res = array("code" => 100, "success" => true, "message" => "Request status updated");
			   	}
			   	$em->flush();
			   	return $res;
	   		}
	   	}
		return $res = array("code" => 400, "success" => false, "message" => "Request was not found");
	}
}
