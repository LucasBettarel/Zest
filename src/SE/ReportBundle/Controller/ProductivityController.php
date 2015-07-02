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

	public function refreshAction()
	{
		/*
		select sapimports not processed by date (1)
		select userinput not processed by date (several)
		check matches
		
		for every single element : add in review input error : sap not imported or no input 
		
		for every couples sap/user :
		get shift+team+date

			for each input entry :
				if activity : picking/putaway
					get employee sesa or input entry sesa if different
					get trackable activities (picking/putaway) regular+OT hours

					in saprf.getdate.getteam.getshift.getsesa :
						TOlines = countrows in regular hours (between shift.start and shift.end)
						TOlines += countrows in OT hours (between ot.start and ot.end)

						for each saprf.row selected -> bool process = true
						update (add) TOlines to userinput (daily team) table
						update (add) TOlines to input_entry (daily employee) table
						//-> check if prod is well calculated on update
					--
				--
			--
		eof : remaining to lines.count -> go to input error table (//define table structure)
		//??? what else???

		->display data calculated (highchart and data tables)
		-> take apero
		*/
		return $this->render('SEReportBundle:Productivity:prod.html.twig');
	}
}
