<?php

namespace SE\InputBundle\Controller;

use SE\InputBundle\Entity\Employee;
use SE\InputBundle\Form\EmployeeType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class SettingsController extends Controller
{
  	public function employeesAction()
  	{
      $listEmployees = $this->getDoctrine()
        ->getManager()
        ->getRepository('SEInputBundle:Employee')
        ->findAll()
      ;

    	return $this->render('SEInputBundle:Settings:employees.html.twig', array(
        'listEmployees' => $listEmployees
      ));
  	}

  	public function employees_addAction(Request $request)
  	{
      $employee = new Employee();
      $form = $this->createForm(new EmployeeType(), $employee);

      if ($form->handleRequest($request)->isValid()) {
        $em = $this->getDoctrine()->getManager();
        $em->persist($employee);
        $em->flush();

      $request->getSession()->getFlashBag()->add('notice', 'new employee entry saved');

      return $this->redirect($this->generateUrl('se_input_employees'));
      }

      return $this->render('SEInputBundle:Settings:employees_add.html.twig', array(
      'form' => $form->createView(),
      ));
  	}

  	public function activitiesAction()
  	{
      $listActivities = $this->getDoctrine()
        ->getManager()
        ->getRepository('SEInputBundle:Activity')
        ->findAll()
      ;

    	return $this->render('SEInputBundle:Settings:activities.html.twig', array(
        'listActivities' => $listActivities
      ));
  	}

  	public function activities_addAction()
  	{
    	return $this->render('SEInputBundle:Settings:activities_add.html.twig');
  	}
}
