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
	        				//picking or binning
	        				if ($activity.getActivity().getTrackable() == true){
	        					$sesa = $inputEntry.getSesa();
	        					$start = $inputShift.getStartTime();
	        					$end = $inputShift.getEndTime();
	        					$otStart = $activity.getOtStartTime();
	        					$otEnd = getOtEndTime();
	        					$to = $inputEntry.getTotalTo();
	        					$missingTO = array();

	        					//go in saprf and do the shit.
        					    $TOlines = $this->getDoctrine()
								 ->getManager()
								 ->getRepository('SEInputBundle:SAPRF')
								 ->findBy(array('recorded' => null, 'user' => $sesa, 'dateConfirmation' => $inputDate))
								;

								//restrict by hours
								foreach ($TOlines as $line) {
									if(($line.getTimeConfirmation() >= $start and $line.getTimeConfirmation() <= $end) or ($line.getTimeConfirmation() >= $otStart and $line.getTimeConfirmation() <= $otEnd)){
										$to += 1; //ok
										$line.setRecorded(1);
									}
									else{
										$missingTO[] = $line; //pas ok

									}
								}

								//update in inputentry the to lines
								$inputEntry.setTotalTo($to);

								//add not affected tolines to review input error
								if(count($missingTO) > 0){
									//add to review error
								}
	        				}
	        			}
	        		}

	        		//process finished -> +1 input done in sapImport
	        		// faire un check error avant
	        		$sapToProcessDay.setInputs($sapToProcessDay.getInputs() + 1);

	        		if($sapToProcessDay.getInputs() == 15){ //5 teams*3shifts -> should be changed to team.count*team.shift.count
	        			$sapToProcessDay.setProcess(1);
	        		}

	        		$inputToProcessDay.setProcess(1);
	        	}
	        	else{
	        		//sapImport not done->add to error review (process = 0)
	        	}
        	}//foreach sap
	
	        //calculate new to number + new prod ah ah
			$inputToProcessDay.computeHours();

        }//foreach input

		//check inputs nb in sapImports , and if manque des inputs -> add to error review
        //ni l'un ni l'autre : special error -> check if all date until today exist in sapImports

        //select userinput to display (=all basically -> do better with ajax or something)
		$UserInputDisplay = $this->getDoctrine()
		 ->getManager()
         ->getRepository('SEInputBundle:UserInput')
         ->findBy(array('process' => 1))
        ;

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
    	return $this->render('SEReportBundle:Productivity:prod.html.twig', array(
    		'UserInputDisplay' => $UserInputDisplay));
	}

	public function menuAction()
	{
		return $this->render('SEReportBundle:Productivity:menu.html.twig');
	}

}
