<?php

namespace SE\ReportBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use SE\InputBundle\Entity\SapImports;
use SE\InputBundle\Entity\UserInput;
use SE\InputBundle\Entity\SAPRF;
use SE\InputBundle\Entity\InputReview;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;

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
		$jsonTotalData = array(
			'dates' => array(),
			'hub' => array(
				0 => array('to' => 0,'mh' => 0,'hc' => 0,'tr' => 0,'ab' => 0,'ot' => 0,'wh' => 0),
				1 => array('to' => 0,'mh' => 0,'hc' => 0,'tr' => 0,'ab' => 0,'ot' => 0,'wh' => 0),
				2 => array()),
			'out4' => array(
				0 => array('to' => 0,'mh' => 0,'hc' => 0,'tr' => 0,'ab' => 0,'ot' => 0,'wh' => 0),
				1 => array('to' => 0,'mh' => 0,'hc' => 0,'tr' => 0,'ab' => 0,'ot' => 0,'wh' => 0),
				2 => array()),
			'out4s1' => array(
				0 => array('to' => 0,'mh' => 0,'hc' => 0,'tr' => 0,'ab' => 0,'ot' => 0,'wh' => 0),
				1 => array('to' => 0,'mh' => 0,'hc' => 0,'tr' => 0,'ab' => 0,'ot' => 0,'wh' => 0),
				2 => array()),
			'out4s2' => array(
				0 => array('to' => 0,'mh' => 0,'hc' => 0,'tr' => 0,'ab' => 0,'ot' => 0,'wh' => 0),
				1 => array('to' => 0,'mh' => 0,'hc' => 0,'tr' => 0,'ab' => 0,'ot' => 0,'wh' => 0),
				2 => array()),
			'out4s3' => array(
				0 => array('to' => 0,'mh' => 0,'hc' => 0,'tr' => 0,'ab' => 0,'ot' => 0,'wh' => 0),
				1 => array('to' => 0,'mh' => 0,'hc' => 0,'tr' => 0,'ab' => 0,'ot' => 0,'wh' => 0),
				2 => array()),
			'in4' => array(
				0 => array('to' => 0,'mh' => 0,'hc' => 0,'tr' => 0,'ab' => 0,'ot' => 0,'wh' => 0),
				1 => array('to' => 0,'mh' => 0,'hc' => 0,'tr' => 0,'ab' => 0,'ot' => 0,'wh' => 0),
				2 => array()),
			'in4s1' => array(
				0 => array('to' => 0,'mh' => 0,'hc' => 0,'tr' => 0,'ab' => 0,'ot' => 0,'wh' => 0),
				1 => array('to' => 0,'mh' => 0,'hc' => 0,'tr' => 0,'ab' => 0,'ot' => 0,'wh' => 0),
				2 => array()),
			'in4s2' => array(
				0 => array('to' => 0,'mh' => 0,'hc' => 0,'tr' => 0,'ab' => 0,'ot' => 0,'wh' => 0),
				1 => array('to' => 0,'mh' => 0,'hc' => 0,'tr' => 0,'ab' => 0,'ot' => 0,'wh' => 0),
				2 => array()),
			'in4s3' => array(
				0 => array('to' => 0,'mh' => 0,'hc' => 0,'tr' => 0,'ab' => 0,'ot' => 0,'wh' => 0),
				1 => array('to' => 0,'mh' => 0,'hc' => 0,'tr' => 0,'ab' => 0,'ot' => 0,'wh' => 0),
				2 => array()),
			'out3' => array(
				0 => array('to' => 0,'mh' => 0,'hc' => 0,'tr' => 0,'ab' => 0,'ot' => 0,'wh' => 0),
				1 => array('to' => 0,'mh' => 0,'hc' => 0,'tr' => 0,'ab' => 0,'ot' => 0,'wh' => 0),
				2 => array()),
			'in3' => array(
				0 => array('to' => 0,'mh' => 0,'hc' => 0,'tr' => 0,'ab' => 0,'ot' => 0,'wh' => 0),
				1 => array('to' => 0,'mh' => 0,'hc' => 0,'tr' => 0,'ab' => 0,'ot' => 0,'wh' => 0),
				2 => array()),
			'rel' => array(
				0 => array('to' => 0,'mh' => 0,'hc' => 0,'tr' => 0,'ab' => 0,'ot' => 0,'wh' => 0),
				1 => array('to' => 0,'mh' => 0,'hc' => 0,'tr' => 0,'ab' => 0,'ot' => 0,'wh' => 0),
				2 => array()),
			'rels1' => array(
				0 => array('to' => 0,'mh' => 0,'hc' => 0,'tr' => 0,'ab' => 0,'ot' => 0,'wh' => 0),
				1 => array('to' => 0,'mh' => 0,'hc' => 0,'tr' => 0,'ab' => 0,'ot' => 0,'wh' => 0),
				2 => array()),
			'rels2' => array(
				0 => array('to' => 0,'mh' => 0,'hc' => 0,'tr' => 0,'ab' => 0,'ot' => 0,'wh' => 0),
				1 => array('to' => 0,'mh' => 0,'hc' => 0,'tr' => 0,'ab' => 0,'ot' => 0,'wh' => 0),
				2 => array()),
			'ada' => array(
				0 => array('to' => 0,'mh' => 0,'hc' => 0,'tr' => 0,'ab' => 0,'ot' => 0,'wh' => 0),
				1 => array('to' => 0,'mh' => 0,'hc' => 0,'tr' => 0,'ab' => 0,'ot' => 0,'wh' => 0),
				2 => array()),
			'adas1' => array(
				0 => array('to' => 0,'mh' => 0,'hc' => 0,'tr' => 0,'ab' => 0,'ot' => 0,'wh' => 0),
				1 => array('to' => 0,'mh' => 0,'hc' => 0,'tr' => 0,'ab' => 0,'ot' => 0,'wh' => 0),
				2 => array()),
			'adas2' => array(
				0 => array('to' => 0,'mh' => 0,'hc' => 0,'tr' => 0,'ab' => 0,'ot' => 0,'wh' => 0),
				1 => array('to' => 0,'mh' => 0,'hc' => 0,'tr' => 0,'ab' => 0,'ot' => 0,'wh' => 0),
				2 => array()),
			'tas' => array(
				0 => array('to' => 0,'mh' => 0,'hc' => 0,'tr' => 0,'ab' => 0,'ot' => 0,'wh' => 0),
				1 => array('to' => 0,'mh' => 0,'hc' => 0,'tr' => 0,'ab' => 0,'ot' => 0,'wh' => 0),
				2 => array()),
			'tass1' => array(
				0 => array('to' => 0,'mh' => 0,'hc' => 0,'tr' => 0,'ab' => 0,'ot' => 0,'wh' => 0),
				1 => array('to' => 0,'mh' => 0,'hc' => 0,'tr' => 0,'ab' => 0,'ot' => 0,'wh' => 0),
				2 => array()),
			'tass2' => array(
				0 => array('to' => 0,'mh' => 0,'hc' => 0,'tr' => 0,'ab' => 0,'ot' => 0,'wh' => 0),
				1 => array('to' => 0,'mh' => 0,'hc' => 0,'tr' => 0,'ab' => 0,'ot' => 0,'wh' => 0),
				2 => array()),
		);

		for ($i=0; $i < $daydiff; $i++) { 
    		$dateCheck = new \DateTime();
    		$dateCheck->setTime(00, 00, 00)->modify( '-'.($i+1).' day' );
			$toutdesuite = $dateCheck->format("Y-m-d");
			array_unshift($jsonTotalData['dates'], $dateCheck->format("d-m"));
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

			$RelWorking = 0;
			$RelTo = 0;
			$Rels1Working = 0;
			$Rels1To = 0;
			$Rels2Working = 0;
			$Rels2To = 0;
			$AdaWorking = 0;
			$AdaTo = 0;
			$Adas1Working = 0;
			$Adas1To = 0;
			$Adas2Working = 0;
			$Adas2To = 0;
			$TasWorking = 0;
			$TasTo = 0;
			$Tass1Working = 0;
			$Tass1To = 0;
			$Tass2Working = 0;
			$Tass2To = 0;

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
	    						
    						}elseif ($j == 6) {
    							$RelWorking += $userInput->getTotalWorkingHoursInput();
    							$RelTo += $userInput->getTotalToInput();
    							$jsonTotalData = $this->loadTotalData($jsonTotalData, 'rel', $i, $userInput);

    							if($k == 0){
    								$Rels1Working += $userInput->getTotalWorkingHoursInput();
    								$Rels1To += $userInput->getTotalToInput();
    								$jsonTotalData = $this->loadTotalData($jsonTotalData, 'rels1', $i, $userInput);
	    						
    							}elseif($k == 1){
    								$Rels2Working += $userInput->getTotalWorkingHoursInput();
    								$Rels2To += $userInput->getTotalToInput();
    								$jsonTotalData = $this->loadTotalData($jsonTotalData, 'rels2', $i, $userInput);
	    					
    							}
    						}elseif ($j == 8) {
    							$AdaWorking += $userInput->getTotalWorkingHoursInput();
    							$AdaTo += $userInput->getTotalToInput();
    							$jsonTotalData = $this->loadTotalData($jsonTotalData, 'ada', $i, $userInput);

    							if($k == 0){
    								$Adas1Working += $userInput->getTotalWorkingHoursInput();
    								$Adas1To += $userInput->getTotalToInput();
    								$jsonTotalData = $this->loadTotalData($jsonTotalData, 'adas1', $i, $userInput);
	    						
    							}elseif($k == 1){
    								$Adas2Working += $userInput->getTotalWorkingHoursInput();
    								$Adas2To += $userInput->getTotalToInput();
    								$jsonTotalData = $this->loadTotalData($jsonTotalData, 'adas2', $i, $userInput);
	    					
    							}
    						}elseif ($j == 9) {
    							$TasWorking += $userInput->getTotalWorkingHoursInput();
    							$TasTo += $userInput->getTotalToInput();
    							$jsonTotalData = $this->loadTotalData($jsonTotalData, 'tas', $i, $userInput);

    							if($k == 0){
    								$Tass1Working += $userInput->getTotalWorkingHoursInput();
    								$Tass1To += $userInput->getTotalToInput();
    								$jsonTotalData = $this->loadTotalData($jsonTotalData, 'tass1', $i, $userInput);
	    						
    							}elseif($k == 1){
    								$Tass2Working += $userInput->getTotalWorkingHoursInput();
    								$Tass2To += $userInput->getTotalToInput();
    								$jsonTotalData = $this->loadTotalData($jsonTotalData, 'tass2', $i, $userInput);
	    					
    							}
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

    		$RelProd = ($RelWorking != 0 ? $RelTo/$RelWorking : 0);
    		$Rels1Prod = ($Rels1Working != 0 ? $Rels1To/$Rels1Working : 0);
    		$Rels2Prod = ($Rels2Working != 0 ? $Rels2To/$Rels2Working : 0);
    		$AdaProd = ($AdaWorking != 0 ? $AdaTo/$AdaWorking : 0);
    		$Adas1Prod = ($Adas1Working != 0 ? $Adas1To/$Adas1Working : 0);
    		$Adas2Prod = ($Adas2Working != 0 ? $Adas2To/$Adas2Working : 0);
    		$TasProd = ($TasWorking != 0 ? $TasTo/$TasWorking : 0);
    		$Tass1Prod = ($Tass1Working != 0 ? $Tass1To/$Tass1Working : 0);
    		$Tass2Prod = ($Tass2Working != 0 ? $Tass2To/$Tass2Working : 0);
    		
    		array_unshift($jsonTotalData['hub'][2], round($hubProd, 1));
    		array_unshift($jsonTotalData['out4'][2], round($Out4Prod,1));
    		array_unshift($jsonTotalData['out4s1'][2], round($Out4s1Prod,1));
    		array_unshift($jsonTotalData['out4s2'][2], round($Out4s2Prod,1));
    		array_unshift($jsonTotalData['out4s3'][2], round($Out4s3Prod,1));
    		array_unshift($jsonTotalData['out3'][2], round($Out3Prod,1));
    		array_unshift($jsonTotalData['in4'][2], round($In4Prod,1));
    		array_unshift($jsonTotalData['in4s1'][2], round($In4s1Prod,1));
    		array_unshift($jsonTotalData['in4s2'][2], round($In4s2Prod,1));
    		array_unshift($jsonTotalData['in4s3'][2], round($In4s3Prod,1));
    		array_unshift($jsonTotalData['in3'][2], round($In3Prod,1));

    		array_unshift($jsonTotalData['rel'][2], round($RelProd,1));
    		array_unshift($jsonTotalData['rels1'][2], round($Rels1Prod,1));
    		array_unshift($jsonTotalData['rels2'][2], round($Rels2Prod,1));
    		array_unshift($jsonTotalData['ada'][2], round($AdaProd,1));
    		array_unshift($jsonTotalData['adas1'][2], round($Adas1Prod,1));
    		array_unshift($jsonTotalData['adas2'][2], round($Adas2Prod,1));
    		array_unshift($jsonTotalData['tas'][2], round($TasProd,1));
    		array_unshift($jsonTotalData['tass1'][2], round($Tass1Prod,1));
    		array_unshift($jsonTotalData['tass2'][2], round($Tass2Prod,1));

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
										if( ( $regularReverse and ($timeConf <= $end) and ($timeConf >= $start) ) or ( !$regularReverse and ( ( $timeConf >= $start ) or ( $timeConf <= $end ) ) ) ) { //regular hours
											if( $line->getSourceStorageType() == '902' and $activity->getActivity()->getId() == 3){ //putaway
												$to += 1; //ok
												$line->setRecorded(1);
											}elseif( $line->getSourceStorageType() != '902' and $activity->getActivity()->getId() == 2 ){//picking
												$to += 1; //ok
												$line->setRecorded(1);
											}else{
												$missingTO += 1; //pas ok
											}	
										}
										elseif ( ( ( $otReverse and ($timeConf <= $otEnd) and ($timeConf >= $otStart) ) or ( !$otReverse and ( ( $timeConf >= $otStart ) or ( $timeConf <= $otEnd ) ) ) ) and $activity->getOtHours() > 0 ) { //overtime 
											if( $line->getSourceStorageType() == '902' and $activity->getActivity()->getId() == 3){ //putaway
												$to += 1; //ok
												$line->setRecorded(1);
											}elseif( $line->getSourceStorageType() != '902' and $activity->getActivity()->getId() == 2 ){//picking
												$to += 1; //ok
												$line->setRecorded(1);
											}else{
												$missingTO += 1; //pas ok
											}
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

	        		//add manual TO Lines
	        		$manualTo = $inputToProcessDay->getManualTo();
	        		$manualTOlines = $em->getRepository('SEInputBundle:SAPRF')->getManualTo($inputDate, $inputTeam->getId());

	        		if($manualTOlines){
	        			foreach ($manualTOlines as $manualToLine) {
	        				$timeConf = $manualToLine->getTimeConfirmation(); 
							//if inside right time interval + to line not already affected
							if($manualToLine->getRecorded() == 0){ 
								if( ( $regularReverse and ($timeConf <= $end) and ($timeConf >= $start) ) or ( !$regularReverse and ( ( $timeConf >= $start ) or ( $timeConf <= $end ) ) ) ) { //regular hours
									$manualTo += 1; //ok
									$manualToLine->setRecorded(1);	
								}
							}
	        			}
	        			$inputToProcessDay->setManualTo($manualTo);
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
			$TotalData[$team][0]['wh'] += $userInput->getTotalWorkingHoursInput();
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
		$TotalData[$team][1]['wh'] += $userInput->getTotalWorkingHoursInput();
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
}
