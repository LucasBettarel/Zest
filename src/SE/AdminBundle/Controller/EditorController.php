<?php

namespace SE\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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

}
