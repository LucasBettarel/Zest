<?php

namespace SE\InputBundle\Controller;

use SE\InputBundle\Entity\Employee;
use SE\InputBundle\Entity\UserInput;
use SE\InputBundle\Form\UserInputType;
use SE\InputBundle\Entity\InputReview;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class EntryController extends Controller
{
	public function inputAction(Request $request)
	{
    $listEmployees = $this->getDoctrine()
      ->getManager()
      ->getRepository('SEInputBundle:Employee')
      ->findAll()
    ;

    //foreach list employee-> if activity=null -> id = 0 

    $EmployeeCount = sizeof($listEmployees);
  
    $userInput = new UserInput();
    $form = $this->createForm(new UserInputType(), $userInput);

    if ($form->handleRequest($request)->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($userInput);
      $em->flush();

    $request->getSession()->getFlashBag()->add('notice', 'new working hours entry saved');

    return $this->redirect($this->generateUrl('se_input_review'));
    }

    return $this->render('SEInputBundle:Entry:input_form.html.twig', array(
    'form' => $form->createView(),
    'EmployeeCount' => $EmployeeCount,
    'listEmployees' => $listEmployees
    ));
	}

	public function menuAction()
  	{
    	return $this->render('SEInputBundle:Entry:menu.html.twig');
  	}

  public function welcomeAction()
    {
      return $this->render('SEInputBundle:Entry:welcome.html.twig');
    }

  public function reviewAction()
    {
      $em = $this->getDoctrine()->getManager();

      //select errors
      $importErrors = $em->getRepository('SEInputBundle:InputReview')
       ->findBy(array('type' => $em->getRepository('SEInputBundle:TypeIssue')->find(1)));

      $inputErrors = $em->getRepository('SEInputBundle:InputReview')
       ->findBy(array('type' => $em->getRepository('SEInputBundle:TypeIssue')->find(2)));

      $toErrors = $em->getRepository('SEInputBundle:InputReview')
       ->findBy(array('type' => $em->getRepository('SEInputBundle:TypeIssue')->find(3)));

      $hourErrors = $em->getRepository('SEInputBundle:InputReview')
       ->findBy(array('type' => $em->getRepository('SEInputBundle:TypeIssue')->find(4)));

      $userInputs = $em->getRepository('SEInputBundle:UserInput')
       ->getLastMonth();

      return $this->render('SEInputBundle:Entry:review.html.twig', array(
        'importErrors' => $importErrors,
        'inputErrors' => $inputErrors,
        'toErrors' => $toErrors,
        'hourErrors' => $hourErrors,
        'userInputs' => $userInputs
      ));
    }
}
