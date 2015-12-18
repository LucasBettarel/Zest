<?php

namespace SE\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class EditorController extends Controller
{
    public function editorAction()
    {
    	$em = $this->getDoctrine()->getManager();
    	$requests = $em->getRepository('SEInputBundle:EditorEntry')->findAll();
        return $this->render('SEAdminBundle:Editor:editor.html.twig', array(
                'requests' => $requests,
            ));
    }

    public function currentEntryAction()
  	{ 
	    $em = $this->getDoctrine()->getManager();
	    $req = $this->get('request');        
	    $current = $req->get('current'); 
	    $request = $req->get('request');
	    
	   	$entry = $em->getRepository('SEInputBundle:InputEntry')->find($current);
	   	$editor = $em->getRepository('SEInputBundle:EditorEntry')->find($request);
		
		$response = array('table' => $this->render('SEAdminBundle:Editor:editor-details.html.twig', array('request' => $editor, 'entry' => $entry))->getContent());
	    
	    return new Response(json_encode($response)); 
	}

    public function acceptAction()
  	{ 
	    $em = $this->getDoctrine()->getManager();
	    $req = $this->get('request');        
	    $requestId = $req->get('id');
  	    $accept = $em->getRepository('SEInputBundle:EditorStatus')->find(2);
  	    $editor = $this->get('se_admin.editor');
	    
	    if($requestId){
	   		$request = $em->getRepository('SEInputBundle:EditorEntry')->find($requestId);
	   		if($request){
	   			$editorType = $request->getEditorType()->getId();

	   			//addition
	   			if($editorType == 1){

	   			}
	   			//edition
	   			elseif($editorType == 2){

	   			}
	   			//deletion
	   			elseif($editorType == 3){
	   				if($request->getInputEntry()){
	   					//////////////////////////////////////////////////////////////REPENDRE LA
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
					      $deleteEntry->getUserInput()->setProcess(0);
					      $em->remove($deleteEntry);
					      $em->flush();
					      $res = array('code' => 100, "success" => true, "message" => "Entry deleted, TO Lines resetted");
					    }else{$res = array("code" => 400, "success" => false, "message" => "Entry to delete was not found");}  
	   				}else{$res = array("code" => 400, "success" => false, "message" => "Entry to delete was not found");}
	   			}

	   			return new JsonResponse($res);
	   		}
	   	}
		return new JsonResponse(array("code" => 400, "success" => false, "message" => "Request was not found"));
	}

    public function rejectAction()
  	{ 
	    $em = $this->getDoctrine()->getManager();
	    $req = $this->get('request');        
	    $request = $req->get('id');
	    $reject = $em->getRepository('SEInputBundle:EditorStatus')->find(3);
	    
	   	if($request){
	   		$editor = $em->getRepository('SEInputBundle:EditorEntry')->find($request);
	   		if($editor){
	   			$editor->setEditorStatus($reject);
	   			if($editor->getInputEntry()){
	   				$entry = $em->getRepository('SEInputBundle:InputEntry')->find($editor->getInputEntry()->getId());
	   				if($entry){
	   					$entry->setEditorStatus($reject);
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
			   	return new JsonResponse($res);
	   		}
	   	}
		return new JsonResponse(array("code" => 400, "success" => false, "message" => "Request was not found"));
	}

    public function ignoreAction()
  	{ 
	    $em = $this->getDoctrine()->getManager();
	    $req = $this->get('request');        
	    $request = $req->get('id');
	    $ignore = $em->getRepository('SEInputBundle:EditorStatus')->find(4);
	    
	   	if($request){
	   		$editor = $em->getRepository('SEInputBundle:EditorEntry')->find($request);
	   		if($editor){
	   			$editor->setEditorStatus($ignore);
	   			if($editor->getInputEntry()){
	   				$entry = $em->getRepository('SEInputBundle:InputEntry')->find($editor->getInputEntry()->getId());
	   				if($entry){
	   					$entry->setEditorStatus($ignore);
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
			   	return new JsonResponse($res);
	   		}
	   	}
		return new JsonResponse(array("code" => 400, "success" => false, "message" => "Request was not found"));
	}
}
