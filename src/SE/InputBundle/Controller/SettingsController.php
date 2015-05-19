<?php

namespace SE\InputBundle\Controller;

use SE\InputBundle\Entity\Employee;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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

  	public function employees_addAction()
  	{
    	return $this->render('SEInputBundle:Settings:employees_add.html.twig');
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
