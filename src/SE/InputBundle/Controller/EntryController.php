<?php

namespace SE\InputBundle\Controller;

use SE\InputBundle\Entity\UserInput;
use SE\InputBundle\Form\UserInputType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class EntryController extends Controller
{
	public function inputAction(Request $request)
	{
	  $userInput = new UserInput();
      $form = $this->createForm(new UserInputType(), $userInput);

      if ($form->handleRequest($request)->isValid()) {
        $em = $this->getDoctrine()->getManager();
        $em->persist($userInput);
        $em->flush();

      $request->getSession()->getFlashBag()->add('notice', 'new working hours entry saved');

      return $this->redirect($this->generateUrl('se_input_home'));
      }

      return $this->render('SEInputBundle:Entry:input.html.twig', array(
      'form' => $form->createView(),
      ));
	}

	public function menuAction()
  	{
    	return $this->render('SEInputBundle:Entry:menu.html.twig');
  	}
}
