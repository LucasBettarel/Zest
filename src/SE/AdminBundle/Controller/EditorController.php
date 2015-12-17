<?php

namespace SE\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

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
	  /*  $request = $this->get('request');        
	    $current = $request->get('current'); 
	    $request = $request->get('request');
	    
	   	$entry = $em->getRepository('SEInputBundle:InputEntry')->find($current);
	   	$editor = $em->getRepository('SEInputBundle:EditorEntry')->find($request);
	*/	
		$response = array('msg' => "");
	    
	    return new Response(json_encode($response)); 
	}

    public function rejectAction()
  	{ 
	    $em = $this->getDoctrine()->getManager();
	    $req = $this->get('request');        
	    $request = $req->get('request');
	    
	   	$editor = $em->getRepository('SEInputBundle:EditorEntry')->find($request);
	   	$entry = $em->getRepository('SEInputBundle:InputEntry')->find($editor->getInputEntry()->getId());

	   	if($editor){$editor->setEditorStatus(3);}
	   	else{ return new Response(json_encode(array("code" => 400, "success" => false, "message" => "the request was not found"))); }
	   	
	   	if($entry){$entry->setEditorStatus(3);}
	   	elseif($editor->getEditorType()->getId() != 1){ return new Response(json_encode(array("code" => 400, "success" => false, "message" => "the entry was not found"))); }
	   	
	   	$em->flush();
		$response = array("code" => 100, "success" => true);
	    
	    return new Response(json_encode($response)); 
	}

    public function ignoreAction()
  	{ 
	    $em = $this->getDoctrine()->getManager();
	    $req = $this->get('request');        
	    $request = $req->get('request');
	    
	   	$editor = $em->getRepository('SEInputBundle:EditorEntry')->find($request);
	   	$entry = $em->getRepository('SEInputBundle:InputEntry')->find($editor->getInputEntry()->getId());

	   	if($editor){$editor->setEditorStatus(4);}
	   	else{ return new Response(json_encode(array("code" => 400, "success" => false, "message" => "the request was not found"))); }
	   	
	   	if($entry){$entry->setEditorStatus(4);}
	   	elseif($editor->getEditorType()->getId() != 1){ return new Response(json_encode(array("code" => 400, "success" => false, "message" => "the entry was not found"))); }
	   	
	   	$em->flush();
		$response = array("code" => 100, "success" => true);
	    
	    return new Response(json_encode($response)); 
	}
}
