<?php

namespace SE\InputBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SettingsController extends Controller
{
  	public function employeesAction()
  	{
    	return $this->render('SEInputBundle:Settings:employees.html.twig');
  	}

  	public function employees_addAction()
  	{
    	return $this->render('SEInputBundle:Settings:employees_add.html.twig');
  	}

  	public function activitiesAction()
  	{
    	return $this->render('SEInputBundle:Settings:activities.html.twig');
  	}

  	public function activities_addAction()
  	{
    	return $this->render('SEInputBundle:Settings:activities_add.html.twig');
  	}
}
