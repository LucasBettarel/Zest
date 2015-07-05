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

        //for each inputToProcess(not processed)
        foreach ($inputToProcess as $inputToProcessDay) {
        	foreach ($sapToProcess as $sapToProcessDay) {
        	
	        	if($inputToProcessDay.getDate() == $sapToProcessDay.getDate()){
	        		//match: input.date present in sap.date

	        		$inputDate = $inputToProcessDay.getDate();
	        		$inputTeam = $inputToProcessDay.getTeam();
	        		$inputShift = $inputToProcessDay.getShift();

	        		foreach ($inputToProcessDay.getInputEntries() as $inputEntry) {
	        			foreach ($inputEntry.getActivityHours() as $activity) {
	        				if ($activity.getActivity().getTrackable() == true){
	        					$sesa = $inputEntry.getSesa();
	        					$hours = $activity.getRegularHours();
	        					$overtime = $activity.getOtHours();
	        					$otStart = $activity.getOtStartTime();
	        					$otEnd = getOtEndTime();

	        					//go in saprf and do the shit.
	        				}
	        			}
	        		}

	        		//process finished -> +1 input done in sapImport
	        		$sapToProcessDay.setInputs($sapToProcessDay.getInputs() + 1);

	        		if($sapToProcessDay.getInputs() == 4){
	        			$sapToProcessDay.setProcess(1);
	        		}

	        		$inputToProcessDay.setProcess(1);
	        	}
	        	else{
	        		//sapImport not done->add to error review (process = 0)
	        	}
        	}//foreach sap
        }//foreach input

		//check inputs nb in sapImports , and if manque des inputs -> add to error review
        //ni l'un ni l'autre : special error -> check if all date until today exist in sapImports


		/*	
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
