<?php

namespace SE\ReportBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use SE\InputBundle\Entity\SapImports;
use SE\InputBundle\Entity\UserInput;
use SE\InputBundle\Entity\SAPRF;
use SE\InputBundle\Entity\InputReview;

class ProductivityController extends Controller
{
	public function indexAction()
	{
		$em = $this->getDoctrine()->getManager();
		$flusher = false;

		//select sapimports not processed (one...)
		$sapToProcess = $em->getRepository('SEInputBundle:SapImports')
         ->findBy(array('process' => 0))
        ;

        //select userinput not processed (...toMany)
		$inputToProcess = $em->getRepository('SEInputBundle:UserInput')
         ->findBy(array('process' => 0))
        ;

        //tous les inputs du dernier mois
        $userInputs = $em->getRepository('SEInputBundle:UserInput')
         ->getLastMonth();

         //test
         $lolo = array();
         $toto = array(); 
         $lala = 'rate!';
         $missingTO = 0;

        //for each inputToProcess(not processed)
        foreach ($inputToProcess as $inputToProcessDay) {
        	$inputUser = $inputToProcessDay->getUser();
        	$inputDate = $inputToProcessDay->getDateInput();
    		$inputTeam = $inputToProcessDay->getTeam();
    		$inputShift = $inputToProcessDay->getShift();
    		$otStart = $inputToProcessDay->getOtStartTime();
			$otEnd = $inputToProcessDay->getOtEndTime();					
    		$toIssue = $em->getRepository('SEInputBundle:TypeIssue')->find(3);
					         		
        	foreach ($sapToProcess as $sapToProcessDay) {
      
	        	if($inputDate->format("Y-m-d") == $sapToProcessDay->getDate()->format("Y-m-d")){
	        		//match: input.date present in sap.date
	        		foreach ($inputToProcessDay->getInputEntries() as $inputEntry) {
	        			foreach ($inputEntry->getActivityHours() as $activity) {
	        				//picking or putaway
	        				if ($activity->getActivity()->getTrackable() == true){
	        					$sesa = $inputEntry->getSesa();
	        					$start = $inputShift->getStartTime();
	        					$end = $inputShift->getEndTime();
	        					$to = $inputEntry->getTotalTo();
	        					$regularReverse = ($start > $end ? true : false);
	        					$otReverse = ($otStart > $otEnd ? true : false);
	
						 		//go in saprf and do the shit.
        					    $TOlines = $em->getRepository('SEInputBundle:SAPRF')->getTo($inputDate, $sesa);
								$lala = 'relousss!';
								//restrict by hours
								foreach ($TOlines as $line) {
									$timeConf = $line->getTimeConfirmation(); 
									$lala = 'somethng!';
								if( ( $regularReverse and ($timeConf <= $end) and ($timeConf >= $start) ) or ( !$regularReverse and ( ( $timeConf >= $start ) or ( $timeConf <= $end ) ) ) or ( $otReverse and ($timeConf <= $otEnd) and ($timeConf >= $otStart) ) or ( !$otReverse and ( ( $timeConf >= $otStart ) or ( $timeConf <= $otEnd ) ) ) ) {
										$to += 1; //ok
										$line->setRecorded(1);
									$lala = 'affected!';
									}
									else{
										$missingTO += 1; //pas ok
									}
								}

								//update in inputentry the to lines
								$inputEntry->setTotalTo($to);
								$toto[] = $to;

								//add not affected tolines (those in shift time for now, by team/area later) to review input error
								if(count($missingTO) > 0 and !($em->getRepository('SEInputBundle:InputReview')->findOneBy(array('date' => $inputDate, 'type' => $toIssue, 'team' => $inputTeam, 'shift' =>  $inputShift))) ){
					         		$missingHour = new InputReview();
					         		$missingHour->setDate($inputDate);
					         		$missingHour->setType($toIssue);
					         		$missingHour->setToerror($missingTO);
					         		$missingHour->setUser($inputUser);
					         		$missingHour->setTeam($inputTeam);
					         		$missingHour->setShift($inputShift);
					         		$missingHour->setStatus(0);

					  		        $em->persist($missingHour);
					  		        $flusher = true;
								}
	        				}
	        			}
	        		}

	        		//process finished -> +1 input done in sapImport
	        		//faire un check error avant
					$sapToProcessDay->setInputs($sapToProcessDay->getInputs() + 1);

	        		if($sapToProcessDay->getInputs() == 10){ //team.count*team.shift.count
	        			$sapToProcessDay->setProcess(1);
	        		}

	        		$inputToProcessDay->setProcess(1);
	        	}
	       	}
	        //calculate new to number + new prod ah ah
			$inputToProcessDay->computeHours();
        }//foreach input

	 	$today = new \DateTime();
   		$today->setTime(00, 00, 00);
    	$lastMonth = new \DateTime();
    	$lastMonth->setTime(00, 00, 00)->modify( '-'.(date('j')-1).' day' );
		$daydiff = $today->diff($lastMonth)->days;
		$teams = $em->getRepository('SEInputBundle:Team')->findAll();
		$teamCount = count($teams);
		$shifts = $em->getRepository('SEInputBundle:Shift')->findAll();
		$inputIssue = $em->getRepository('SEInputBundle:TypeIssue')->find(2);
		$importIssue = $em->getRepository('SEInputBundle:TypeIssue')->find(1);
		$found = false;
		$jsonData = array();
		$jsonCategories = array();
		$jsonHub = array();

		for ($i=0; $i < $daydiff; $i++) { 
    		$dateCheck = new \DateTime();
    		$dateCheck->setTime(00, 00, 00)->modify( '-'.($i+1).' day' );
			$toutdesuite = $dateCheck->format("Y-m-d");
			array_unshift($jsonCategories, $dateCheck->format("d-m"));
			$hubWorking = 0;
			$hubTo = 0;
			for ($j=0; $j < $teamCount; $j++) {
    			$shiftCount = $teams[$j]->getShiftnb();
    			for ($k=0; $k < $shiftCount; $k++) {
    				foreach ($userInputs as $userInput) {
    					if(($userInput->getDateInput()->format("Y-m-d") == $toutdesuite) and ($userInput->getTeam() == $teams[$j]) and ($userInput->getShift()->getId() == $k+1)){
    						$found = true;

    						$hubWorking += $userInput->getTotalWorkingHoursInput();
    						$hubTo += $userInput->getTotalToInput();

    						//to delete?
    						$jsonData[$dateCheck->format("d-m")][$j+1][$k+1] = array('to' => $userInput->getTotalToInput(), 
													 							   'hours' => $userInput->getTotalHoursInput(), 
													 							   'working' => $userInput->getTotalWorkingHoursInput(),
													 							   'overtime' => $userInput->getTotalOvertimeInput(),
													 							   'headcount' => sizeof($userInput->getInputEntries()),
													 							   'prod' => $userInput->getTotalProdInput(),
													 							   );		       
    					}
    				}

    				if(!$found){
    					//array nul pour les donnees non remplies
	    				$jsonData[$dateCheck->format("d-m")][$j+1][$k+1] = array('to' => 0, 
												 							   'hours' => 0, 
												 							   'working' => 0,
												 							   'overtime' => 0,
												 							   'headcount' => 0,
												 							   'prod' => 0,
												 							   );
	    			}

    				//faire la query en amont et faire in array... ou autre
    				//dans tous les inputs qu'on a, aucun correspond a celui qui devrait etre, donc on persist l'erreur si c'est pas deja fait
    				if(!($em->getRepository('SEInputBundle:InputReview')->findOneBy(array('date' => $dateCheck, 'type' => $inputIssue, 'team' => $teams[$j], 'shift' =>  $shifts[$k]))) and !$found){
			    		$missinginput = new InputReview();
			     		$missinginput->setDate($dateCheck);
			     		$missinginput->setType($inputIssue);
			     		$missinginput->setStatus(0);
						$missinginput->setTeam($teams[$j]);
						$missinginput->setShift($shifts[$k]);
				        $em->persist($missinginput);
				        $flusher = true;
				    }
					$found = false;		
    			}
    		}

    		//on calcule la prod quotidienne du hub
    		$hubProd = ($hubWorking != 0 ? $hubTo/$hubWorking : 0);
    		$jsonHub[] = $hubProd;

    		//pour chaque jour depuis le debut du mois, ajouter les sap imports manquants.
    		$already = $em->getRepository('SEInputBundle:InputReview')->findOneBy(array(
		                   'date' => $dateCheck,
		                   'type' => $importIssue
		                ));
    		if(!$already){
		        	//sapImport not done and not recorded yet->add to error review (process = 0)
		        	$missingimport = new InputReview();
	         		$missingimport->setDate($dateCheck);
	         		$missingimport->setType($importIssue);
	         		$missingimport->setStatus(0);
	  		        $em->persist($missingimport);
	  		        $flusher = true;
		        }

    	}
	    if ($flusher){
			$em->flush();
			$em->clear();
			$flusher = false;
		}
 	
 		//to remove
 		$yesterday = new \DateTime();
       	$yesterday->setTime(00, 00, 00);
		$yesterday->modify( '- 1 day' );

        //select userinput to display (=all basically -> do better with ajax or something) //to remove
		$yesterdayInput = $em->getRepository('SEInputBundle:UserInput')
         ->findOneBy(array('dateInput' => $yesterday ));

    	return $this->render('SEReportBundle:Productivity:prod.html.twig', array(
    		'sapToProcess' => $sapToProcess,
    		'inputToProcess' => $inputToProcess,
    		'missingTO' => $missingTO,
    		'lala' => $lala, 
    		'lolo' => $lolo,
    		'toto' => $toto,
    		'lastMonthInputs' => $userInputs,
    		'yesterdayInput' => $yesterdayInput,
    		'yesterday' => $yesterday,
    		'jsonData' => json_encode($jsonData, JSON_PRETTY_PRINT),
    		'jsonCategories' => json_encode($jsonCategories, JSON_PRETTY_PRINT),
    		'jsonHub' => json_encode($jsonHub, JSON_PRETTY_PRINT),
    		
    		));
	}

	public function menuAction()
	{
		return $this->render('SEReportBundle:Productivity:menu.html.twig');
	}

}
