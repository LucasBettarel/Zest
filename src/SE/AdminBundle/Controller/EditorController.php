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
    $request = $this->get('request');        
    $current = $request->get('current'); 
    $request = $request->get('request');
    
   	$entry = $em->getRepository('SEInputBundle:InputEntry')->find($current);
   	$editor = $em->getRepository('SEInputBundle:EditorEntry')->find($request);
	
	$response = array('table' => $this->render('SEAdminBundle:Editor:editor-details.html.twig', array('request' => $editor, 'entry' => $entry))->getContent());
    
    return new Response(json_encode($response)); 
  }

}
