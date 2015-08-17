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

        //tous les inputs du dernier mois
        $userInputs = $em->getRepository('SEInputBundle:UserInput')
         ->getLastMonth();

	 	$today = new \DateTime();
   		$today->setTime(00, 00, 00);
    	$lastMonth = new \DateTime();
    	$lastMonth->setTime(00, 00, 00)->modify( '-'.(date('j')-1).' day' );
		$daydiff = $today->diff($lastMonth)->days;
		$teams = $em->getRepository('SEInputBundle:Team')->getReportingTeams();
		$teamCount = count($teams);
		$shifts = $em->getRepository('SEInputBundle:Shift')->findAll();
		$inputIssue = $em->getRepository('SEInputBundle:TypeIssue')->find(2);
		$importIssue = $em->getRepository('SEInputBundle:TypeIssue')->find(1);
		$found = false;
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
		$jsonTotalData = array(
			'hub' => array(
				0 => array(
					'to' => 0,
					'mh' => 0,
					'hc' => 0,
					'tr' => 0,
					'ab' => 0,
					'ot' => 0
				),
				1 => array(
					'to' => 0,
					'mh' => 0,
					'hc' => 0,
					'tr' => 0,
					'ab' => 0,
					'ot' => 0
				)
			),
			'out4' => array(
				0 => array(
					'to' => 0,
					'mh' => 0,
					'hc' => 0,
					'tr' => 0,
					'ab' => 0,
					'ot' => 0
				),
				1 => array(
					'to' => 0,
					'mh' => 0,
					'hc' => 0,
					'tr' => 0,
					'ab' => 0,
					'ot' => 0
				)
			),
			'out4s1' => array(
				0 => array(
					'to' => 0,
					'mh' => 0,
					'hc' => 0,
					'tr' => 0,
					'ab' => 0,
					'ot' => 0
				),
				1 => array(
					'to' => 0,
					'mh' => 0,
					'hc' => 0,
					'tr' => 0,
					'ab' => 0,
					'ot' => 0
				)
			),
			'out4s2' => array(
				0 => array(
					'to' => 0,
					'mh' => 0,
					'hc' => 0,
					'tr' => 0,
					'ab' => 0,
					'ot' => 0
				),
				1 => array(
					'to' => 0,
					'mh' => 0,
					'hc' => 0,
					'tr' => 0,
					'ab' => 0,
					'ot' => 0
				)
			),
			'out4s3' => array(
				0 => array(
					'to' => 0,
					'mh' => 0,
					'hc' => 0,
					'tr' => 0,
					'ab' => 0,
					'ot' => 0
				),
				1 => array(
					'to' => 0,
					'mh' => 0,
					'hc' => 0,
					'tr' => 0,
					'ab' => 0,
					'ot' => 0
				)
			),
			'in4' => array(
				0 => array(
					'to' => 0,
					'mh' => 0,
					'hc' => 0,
					'tr' => 0,
					'ab' => 0,
					'ot' => 0
				),
				1 => array(
					'to' => 0,
					'mh' => 0,
					'hc' => 0,
					'tr' => 0,
					'ab' => 0,
					'ot' => 0
				)
			),
			'in4s1' => array(
				0 => array(
					'to' => 0,
					'mh' => 0,
					'hc' => 0,
					'tr' => 0,
					'ab' => 0,
					'ot' => 0
				),
				1 => array(
					'to' => 0,
					'mh' => 0,
					'hc' => 0,
					'tr' => 0,
					'ab' => 0,
					'ot' => 0
				)
			),
			'in4s2' => array(
				0 => array(
					'to' => 0,
					'mh' => 0,
					'hc' => 0,
					'tr' => 0,
					'ab' => 0,
					'ot' => 0
				),
				1 => array(
					'to' => 0,
					'mh' => 0,
					'hc' => 0,
					'tr' => 0,
					'ab' => 0,
					'ot' => 0
				)
			),
			'in4s3' => array(
				0 => array(
					'to' => 0,
					'mh' => 0,
					'hc' => 0,
					'tr' => 0,
					'ab' => 0,
					'ot' => 0
				),
				1 => array(
					'to' => 0,
					'mh' => 0,
					'hc' => 0,
					'tr' => 0,
					'ab' => 0,
					'ot' => 0
				)
			),
			'out3' => array(
				0 => array(
					'to' => 0,
					'mh' => 0,
					'hc' => 0,
					'tr' => 0,
					'ab' => 0,
					'ot' => 0
				),
				1 => array(
					'to' => 0,
					'mh' => 0,
					'hc' => 0,
					'tr' => 0,
					'ab' => 0,
					'ot' => 0
				)
			),
			'in3' => array(
				0 => array(
					'to' => 0,
					'mh' => 0,
					'hc' => 0,
					'tr' => 0,
					'ab' => 0,
					'ot' => 0
				),
				1 => array(
					'to' => 0,
					'mh' => 0,
					'hc' => 0,
					'tr' => 0,
					'ab' => 0,
					'ot' => 0
				)
			)
		);

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
    							$jsonTotalData = $this->loadTotalData($jsonTotalData, 'out4', $i, $userInput);
	    						$Out4Working += $userInput->getTotalWorkingHoursInput();
    							$Out4To += $userInput->getTotalToInput();

    							if($k == 0){
    								$Out4s1Working += $userInput->getTotalWorkingHoursInput();
    								$Out4s1To += $userInput->getTotalToInput();
    								$jsonTotalData = $this->loadTotalData($jsonTotalData, 'out4s1', $i, $userInput);
	    						
    							}elseif($k == 1){
    								$Out4s2Working += $userInput->getTotalWorkingHoursInput();
    								$Out4s2To += $userInput->getTotalToInput();
    								$jsonTotalData = $this->loadTotalData($jsonTotalData, 'out4s2', $i, $userInput);
	    						
    							}elseif($k == 2){
    								$Out4s3Working += $userInput->getTotalWorkingHoursInput();
    								$Out4s3To += $userInput->getTotalToInput();
    								$jsonTotalData = $this->loadTotalData($jsonTotalData, 'out4s3', $i, $userInput);
	    						
    							}
    						}elseif ($j == 1) {
    							$In4Working += $userInput->getTotalWorkingHoursInput();
    							$In4To += $userInput->getTotalToInput();
    							$jsonTotalData = $this->loadTotalData($jsonTotalData, 'in4', $i, $userInput);

    							if($k == 0){
    								$In4s1Working += $userInput->getTotalWorkingHoursInput();
    								$In4s1To += $userInput->getTotalToInput();
    								$jsonTotalData = $this->loadTotalData($jsonTotalData, 'in4s1', $i, $userInput);
	    						
    							}elseif($k == 1){
    								$In4s2Working += $userInput->getTotalWorkingHoursInput();
    								$In4s2To += $userInput->getTotalToInput();
    								$jsonTotalData = $this->loadTotalData($jsonTotalData, 'in4s2', $i, $userInput);
	    						
    							}elseif($k == 2){
    								$In4s3Working += $userInput->getTotalWorkingHoursInput();
    								$In4s3To += $userInput->getTotalToInput();
    								$jsonTotalData = $this->loadTotalData($jsonTotalData, 'in4s3', $i, $userInput);
	    						
    							}
    						}elseif ($j == 3) {
    							$Out3Working += $userInput->getTotalWorkingHoursInput();
    							$Out3To += $userInput->getTotalToInput();
    							$jsonTotalData = $this->loadTotalData($jsonTotalData, 'out3', $i, $userInput);
	    						
    						}elseif ($j == 4) {
    							$In3Working += $userInput->getTotalWorkingHoursInput();
    							$In3To += $userInput->getTotalToInput();
    							$jsonTotalData = $this->loadTotalData($jsonTotalData, 'in3', $i, $userInput);
	    						
    						}

    						//total data implement
    						$jsonTotalData = $this->loadTotalData($jsonTotalData, 'hub', $i, $userInput);	
    					}
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
 	
 		return $this->render('SEReportBundle:Productivity:prod.html.twig', array(
    		//'lastMonthInputs' => $userInputs, not used
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
    		'jsonTotalData' => json_encode($jsonTotalData),
    		));
	}

	public function refreshAction()
	{
		$em = $this->getDoctrine()->getManager();

		//select sapimports not processed
		$sapToProcess = $em->getRepository('SEInputBundle:SapImports')
         ->findBy(array('process' => 0))
        ;

        //select userinput not processed (...toMany)
		$inputToProcess = $em->getRepository('SEInputBundle:UserInput')
         ->findBy(array('process' => 0))
        ;

        //var for error recording
   		$toIssue = $em->getRepository('SEInputBundle:TypeIssue')->find(3);
        $missingTO = 0;


        //for each inputToProcess(not processed)
        foreach ($inputToProcess as $inputToProcessDay) {
        	$inputUser = $inputToProcessDay->getUser();
        	$inputDate = $inputToProcessDay->getDateInput();
    		$inputTeam = $inputToProcessDay->getTeam();
    		$inputShift = $inputToProcessDay->getShift();
    		$otStart = $inputToProcessDay->getOtStartTime();
			$otEnd = $inputToProcessDay->getOtEndTime();					
					         		
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
	        					$regularReverse = ($start < $end ? true : false);
	        					$otReverse = ($otStart > $otEnd ? true : false);
	
						 		//go in saprf and do the shit.
        					    $TOlines = $em->getRepository('SEInputBundle:SAPRF')->getTo($inputDate, $sesa);
							
								//restrict by hours
								foreach ($TOlines as $line) {
									$timeConf = $line->getTimeConfirmation(); 
									//if inside right time interval + to line not already affected
									if($line->getRecorded() == 0){ 
										if( ( $regularReverse and ($timeConf <= $end) and ($timeConf >= $start) ) or ( !$regularReverse and ( ( $timeConf >= $start ) or ( $timeConf <= $end ) ) ) or ( $otReverse and ($timeConf <= $otEnd) and ($timeConf >= $otStart) ) or ( !$otReverse and ( ( $timeConf >= $otStart ) or ( $timeConf <= $otEnd ) ) ) ) {
											$to += 1; //ok
											$line->setRecorded(1);
										}
										else{
											$missingTO += 1; //pas ok
										}
									}
								}

								//update in inputentry the to lines
								$inputEntry->setTotalTo($to);

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
								}
	        				}
	        			}
	        		}

	        		//process finished -> +1 input done in sapImport
	        		//faire un check error avant
					$sapToProcessDay->setInputs($sapToProcessDay->getInputs() + 1);

	        		if($sapToProcessDay->getInputs() == 12){ //team.count*team.shift.count
	        			$sapToProcessDay->setProcess(1);
	        		}

	        		$inputToProcessDay->setProcess(1);
	        	}
	       	}
	        //calculate new to number + new prod ah ah
			$inputToProcessDay->computeHours();
        }//foreach input

		$em->flush();

		return $this->redirect($this->generateUrl('se_report_home'));
	}

	public function menuAction()
	{
		return $this->render('SEReportBundle:Productivity:menu.html.twig');
	}

	public function loadTotalData($TotalData, $team, $i, $userInput)
	{
		//total data implement
		if($i == 0){
			//yesterday
			$TotalData[$team][0]['to'] += $userInput->getTotalToInput(); 
			$TotalData[$team][0]['mh'] += $userInput->getTotalHoursInput();
			foreach ($userInput->getInputEntries() as $input) {
			    foreach ($input->getActivityHours() as $a) {
	                if ($a->getActivity()->getId() == 7){
    					$TotalData[$team][0]['tr'] += $a->getRegularHours() + $a->getOtHours();
	                }
				}
				if(!$input->getPresent()){
					$TotalData[$team][0]['ab'] += 1;
				}else{
					$TotalData[$team][0]['hc'] += 1;
				}
			}
			$TotalData[$team][0]['ot'] += $userInput->getTotalOvertimeInput();
		}

		//monthly 
		$TotalData[$team][1]['to'] += $userInput->getTotalToInput(); 
		$TotalData[$team][1]['mh'] += $userInput->getTotalHoursInput();
		foreach ($userInput->getInputEntries() as $input) {
		    foreach ($input->getActivityHours() as $a) {
                if ($a->getActivity()->getId() == 7){
					$TotalData[$team][1]['tr'] += $a->getRegularHours() + $a->getOtHours();
                }
			}
			if(!$input->getPresent()){
				$TotalData[$team][1]['ab'] += 1;
			}else{
				$TotalData[$team][1]['hc'] += 1;
			}
		}
		$TotalData[$team][1]['ot'] += $userInput->getTotalOvertimeInput();

		return $TotalData;
	}

	public function processAction()
	{ 
	    $em = $this->getDoctrine()->getManager();
	    $request = $this->get('request');        
	    $idInput = $request->get('idInput');
	    
	    $ignoreInput = $em->getRepository('SEInputBundle:InputReview')->findOneBy(array('id' => $idInput));

	    if ($ignoreInput){
	    
	      $ignoreInput->setStatus(1);
	      $em->persist($ignoreInput);
	      $em->flush();

	      $response = array("code" => 100, "success" => true);
	    }else{
	      $response = array("code" => 400, "success" => false);
	    }
	    return new Response(json_encode($response)); 
	}

	public function hrAction()
	{ 
	    $em = $this->getDoctrine()->getManager();

	    return $this->render('SEReportBundle:Productivity:hr.html.twig'); 
	}

}
