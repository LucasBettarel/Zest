<?php

namespace SE\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class EditorController extends Controller
{
    public function editorAction()
    {
        return $this->render('SEAdminBundle:Editor:editor.html.twig', array(
                // ...
            ));    }

}
