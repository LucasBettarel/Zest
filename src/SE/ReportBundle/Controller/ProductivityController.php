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
		$jsonOut4 = array();
		$jsonOut4s1 = array();
		$jsonOut4s2 = array();
		$jsonOut4s3 = array();
		$jsonIn4 = array();
		$jsonIn4s1 = array();
		$jsonIn4s2 = array();
		$jsonIn4s3 = array();
		$jsonOut3 = array();
		$jsonIn3 = array();

		for ($i=0; $i < $daydiff; $i++) { 
    		$dateCheck = new \DateTime();
    		$dateCheck->setTime(00, 00, 00)->modify( '-'.($i+1).' day' );
			$toutdesuite = $dateCheck->format("Y-m-d");
			array_unshift($jsonCategories, $dateCheck->format("d-m"));
			$hubWorking = 0;
			$hubTo = 0;

			$Out4Working = 0;
			$Out4To = 0;

			$Out4s1Working = 0;
			$Out4s1To = 0;

			$Out4s2Working = 0;
			$Out4s2To = 0;

			$Out4s3Working = 0;
			$Out4s3To = 0;

			$In4Working = 0;
			$In4To = 0;

			$In4s1Working = 0;
			$In4s1To = 0;

			$In4s2Working = 0;
			$In4s2To = 0;

			$In4s3Working = 0;
			$In4s3To = 0;

			$Out3Working = 0;
			$Out3To = 0;

			$In3Working = 0;
			$In3To = 0;

			for ($j=0; $j < $teamCount; $j++) {
    			$shiftCount = $teams[$j]->getShiftnb();
    			for ($k=0; $k < $shiftCount; $k++) {
    				foreach ($userInputs as $userInput) {
    					if(($userInput->getDateInput()->format("Y-m-d") == $toutdesuite) and ($userInput->getTeam() == $teams[$j]) and ($userInput->getShift()->getId() == $k+1)){
    						$found = true;

    						$hubWorking += $userInput->getTotalWorkingHoursInput();
    						$hubTo += $userInput->getTotalToInput();

    						if($j == 0 || $j == 2){
    							$Out4Working += $userInput->getTotalWorkingHoursInput();
    							$Out4To += $userInput->getTotalToInput();
    							if($k == 0){
    								$Out4s1Working += $userInput->getTotalWorkingHoursInput();
    								$Out4s1To += $userInput->getTotalToInput();
    							}elseif($k == 1){
    								$Out4s2Working += $userInput->getTotalWorkingHoursInput();
    								$Out4s2To += $userInput->getTotalToInput();
    							}elseif($k == 2){
    								$Out4s3Working += $userInput->getTotalWorkingHoursInput();
    								$Out4s3To += $userInput->getTotalToInput();
    							}
    						}elseif ($j == 1) {
    							$In4Working += $userInput->getTotalWorkingHoursInput();
    							$In4To += $userInput->getTotalToInput();
    							if($k == 0){
    								$In4s1Working += $userInput->getTotalWorkingHoursInput();
    								$In4s1To += $userInput->getTotalToInput();
    							}elseif($k == 1){
    								$In4s2Working += $userInput->getTotalWorkingHoursInput();
    								$In4s2To += $userInput->getTotalToInput();
    							}elseif($k == 2){
    								$In4s3Working += $userInput->getTotalWorkingHoursInput();
    								$In4s3To += $userInput->getTotalToInput();
    							}
    						}elseif ($j == 3) {
    							$Out3Working += $userInput->getTotalWorkingHoursInput();
    							$Out3To += $userInput->getTotalToInput();
    						}elseif ($j == 1) {
    							$In3Working += $userInput->getTotalWorkingHoursInput();
    							$In3To += $userInput->getTotalToInput();
    						}



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
    		$Out4Prod = ($Out4Working != 0 ? $Out4To/$Out4Working : 0);
    		$Out4s1Prod = ($Out4s1Working != 0 ? $Out4s1To/$Out4s1Working : 0);
    		$Out4s2Prod = ($Out4s2Working != 0 ? $Out4s2To/$Out4s2Working : 0);
    		$Out4s3Prod = ($Out4s3Working != 0 ? $Out4s3To/$Out4s3Working : 0);   		
    		$Out3Prod = ($Out3Working != 0 ? $Out3To/$Out3Working : 0);
    		$In4Prod = ($In4Working != 0 ? $In4To/$In4Working : 0);
    		$In4s1Prod = ($In4s1Working != 0 ? $In4s1To/$In4s1Working : 0);
    		$In4s2Prod = ($In4s2Working != 0 ? $In4s2To/$In4s2Working : 0);
    		$In4s3Prod = ($In4s3Working != 0 ? $In4s3To/$In4s3Working : 0);
    		$In3Prod = ($In3Working != 0 ? $In3To/$In3Working : 0);
    		array_unshift($jsonHub, round($hubProd, 1));
    		array_unshift($jsonOut4, round($Out4Prod,1));
    		array_unshift($jsonOut4s1, round($Out4s1Prod,1));
    		array_unshift($jsonOut4s2, round($Out4s2Prod,1));
    		array_unshift($jsonOut4s3, round($Out4s3Prod,1));
    		array_unshift($jsonOut3, round($Out3Prod,1));
    		array_unshift($jsonIn4, round($In4Prod,1));
    		array_unshift($jsonIn4s1, round($In4s1Prod,1));
    		array_unshift($jsonIn4s2, round($In4s2Prod,1));
    		array_unshift($jsonIn4s3, round($In4s3Prod,1));
    		array_unshift($jsonIn3, round($In3Prod,1));

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
    		'jsonCategories' => json_encode($jsonCategories),
    		'jsonHub' => json_encode($jsonHub),
    		'jsonOut4' => json_encode($jsonOut4),
    		'jsonOut4s1' => json_encode($jsonOut4s1),
    		'jsonOut4s2' => json_encode($jsonOut4s2),
    		'jsonOut4s3' => json_encode($jsonOut4s3),
    		'jsonOut3' => json_encode($jsonOut3),
    		'jsonIn4' => json_encode($jsonIn4),
    		'jsonIn4s1' => json_encode($jsonIn4s1),
    		'jsonIn4s2' => json_encode($jsonIn4s2),
    		'jsonIn4s3' => json_encode($jsonIn4s3),
    		'jsonIn3' => json_encode($jsonIn3),
    		));
	}

	public function menuAction()
	{
		return $this->render('SEReportBundle:Productivity:menu.html.twig');
	}

}
