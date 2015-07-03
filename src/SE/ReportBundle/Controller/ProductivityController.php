<?php

namespace SE\ReportBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ProductivityController extends Controller
{
	public function indexAction()
	{
		//select sapimports not processed (one...)
		$sapToProcess = $this->getDoctrine()
		 ->getManager()
         ->getRepository('SEInputBundle:SapImports')
         ->findBy(array('process' => 0))
        ;

        //select userinput not processed (...toMany)
		$inputToProcess = $this->getDoctrine()
		 ->getManager()
         ->getRepository('SEInputBundle:UserInput')
         ->findBy(array('process' => 0))
        ;

        //match table
        $matchProcess = array();

        //for each sapimport(not processed)
        foreach ($inputToProcess as $inputToProcessDay) {
        	if(in_array($inputToProcessDay.getDate(), $sapToProcess.getDate())){
        		//match: input.date present in sap.date -> add to match tables
        		$matchProcess[] = array($inputToProcessDay, $sapToProcess.getDate());
        	}
        	else{
        		//sapimport not done->add to error review
        	}

        }
		/*
		
			for each userinput 
			if i.getdate = s.getdate
				->addtocoupletoprocess
			else-> add to reviewinputerror(type=missing_input, ...)
		
		add remaining userinput to > add to reviewinputerror(type=missing_input, ...)

		for every couples sap/user :
		get shift+team+date

			for each input entry :
				if activity : picking/putaway
					get employee sesa or input entry sesa if different
					get trackable activities (picking/putaway) regular+OT hours

					in saprf.getdate.getteam.getshift.getsesa :
						TOlines = countrows in regular hours (between shift.start and shift.end)
						TOlines += countrows in OT hours (between ot.start and ot.end)

						for each saprf.row selected -> bool recorded = true
						update (add) TOlines to userinput (daily team) table
						update (add) TOlines to input_entry (daily employee) table
						//-> check if prod is well calculated on update
					--
				--
			--
		eof : remaining to lines.count(->recorded=0) -> go to input error table (//define table structure)
		//??? what else???

		->display data calculated (highchart and data tables)
		-> take apero
		*/
    	return $this->render('SEReportBundle:Productivity:prod.html.twig');
	}

	public function menuAction()
	{
		return $this->render('SEReportBundle:Productivity:menu.html.twig');
	}

}
