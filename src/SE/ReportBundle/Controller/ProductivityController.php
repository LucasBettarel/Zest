<?php

namespace SE\ReportBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ProductivityController extends Controller
{
	public function indexAction()
	{
    return $this->render('SEReportBundle:Productivity:prod.html.twig');
	}

  public function menuAction()
  {
    return $this->render('SEReportBundle:Productivity:menu.html.twig');
  }
}
