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
 		return $this->render('SEReportBundle:Productivity:monthly.html.twig');
	}

	public function dailyAction()
	{
 		return $this->render('SEReportBundle:Productivity:daily.html.twig');
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
								/*if(count($missingTO) > 0 and !($em->getRepository('SEInputBundle:InputReview')->findOneBy(array('date' => $inputDate, 'type' => $toIssue, 'team' => $inputTeam, 'shift' =>  $inputShift))) ){
					         		$missingHour = new InputReview();
					         		$missingHour->setDate($inputDate);
					         		$missingHour->setType($toIssue);
					         		$missingHour->setToerror($missingTO);
					         		$missingHour->setUser($inputUser);
					         		$missingHour->setTeam($inputTeam);
					         		$missingHour->setShift($inputShift);
					         		$missingHour->setStatus(0);

					  		        $em->persist($missingHour);
								}*/
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

	        		/* Function to improve
	        		if($sapToProcessDay->getInputs() == 12){ //team.count*team.shift.count
	        			$sapToProcessDay->setProcess(1);
	        		}*/
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

	public function monthlyAction()
	{ 
	    $em = $this->getDoctrine()->getManager();
	    $request = $this->get('request');        
	    $year = $request->get('year');
	    $month = $request->get('month');
	    $daysNb = cal_days_in_month(CAL_GREGORIAN, $month, $year);
	    $userInputs = $em->getRepository('SEInputBundle:UserInput')->getMonthInputs($month,$year);
	    $monthlyStructure = array(
						'report' => array('prod' => 0, 'to' => 0,'mh' => 0,'hc' => 0,'tr' => 0,'ab' => 0,'ot' => 0,'wh' => 0, 'mto' => 0, 'le' => 0),
						'activities' => array('cat' => array(), 'data' => array(), 'ke' => array()),
						'prod' => array(),
						'to' => array(),
						'h' => array(),
						'wh' => array()
						);
	    for ($i=1; $i <= $daysNb; $i++) {
	    	$monthlyStructure['prod'][$i] = 0;
	    	$monthlyStructure['to'][$i] = 0;
	    	$monthlyStructure['h'][$i] = 0;
	    	$monthlyStructure['wh'][$i] = 0;
	    }
	    
	    $monthlyJson = $this->createJson($monthlyStructure);
	    for ($i=1; $i <= $daysNb; $i++) {$monthlyJson['days'][] = $i;}

		//fill data
		foreach ($userInputs as $userInput) {
			$day = $userInput->getDateInput()->format('j');
			$team = $userInput->getTeam()->getId();
			if ($team == 3) {$team = 1;}//include local into outbound4
			$shift = $userInput->getShift()->getId();
			$monthlyJson = $this->loadMonthlyData($monthlyJson, $team, $shift, $day, $userInput);
			$monthlyJson = $this->loadMonthlyData($monthlyJson, 0, 0, $day, $userInput);
			$monthlyJson = $this->loadMonthlyData($monthlyJson, $team, 0, $day, $userInput);
		}

		$monthlyJson = $this->forgetKeys($monthlyJson);

		$response = array("code" => 100, "success" => true, "monthlyJson" => $monthlyJson);
	    
	    return new Response(json_encode($response)); 
	}

	public function dayAction()
	{ 
	    $em = $this->getDoctrine()->getManager();
	    $request = $this->get('request');        
	    $date = $request->get('date');
	    $userInputs = $em->getRepository('SEInputBundle:UserInput')->getDayInputs($date);
	    $dailyStructure = array(
							'report' => array('prod' => 0, 'to' => 0,'mh' => 0,'hc' => 0,'tr' => 0,'ab' => 0,'ot' => 0,'wh' => 0, 'mto' => 0, 'le' => 0),
							'activities' => array('cat' => array(), 'data' => array(), 'ke' => array())
						);	    
	    $dailyJson = $this->createJson($dailyStructure);

		//fill data
		foreach ($userInputs as $userInput) {
			$team = $userInput->getTeam()->getId();
			if ($team == 3) {$team = 1;}//include local into outbound4
			$shift = $userInput->getShift()->getId();
			$dailyJson = $this->loadDailyData($dailyJson, $team, $shift, $userInput);
			$dailyJson = $this->loadDailyData($dailyJson, $team, 0, $userInput);
			$dailyJson = $this->loadDailyData($dailyJson, 0, 0, $userInput);
			$view = $this->render('SEReportBundle:Productivity:dailyTable.html.twig', array('input' => $userInput))->getContent();
			$template[] = array($userInput->getTeam()->getName(),$shift,$userInput->getTotalHoursInput(),$userInput->getTotalToInput(),$view);
		}
		$response = array("code" => 100, "success" => true, "dailyJson" => $dailyJson, "template" => $template);
	    
	    return new Response(json_encode($response)); 
	}

	public function loadMonthlyData($data, $t, $s, $d, $u)
	{
		$data[$t][$s]['report']['to'] += $u->getTotalToInput();
		$data[$t][$s]['report']['mh'] += $u->getTotalHoursInput(); 
		$data[$t][$s]['report']['wh'] += $u->getTotalWorkingHoursInput(); 
		$data[$t][$s]['report']['mto'] += $u->getManualTo(); 
		$data[$t][$s]['report']['tr'] += $u->getTotalTrainingHours(); 
		$data[$t][$s]['report']['ab'] += $u->getTotalAbsence(); 
		$data[$t][$s]['report']['hc'] += $u->getTotalHeadcount();
		$data[$t][$s]['report']['ot'] += $u->getTotalOvertimeInput();
		if($data[$t][$s]['report']['wh'] != 0){
			$data[$t][$s]['report']['prod'] = round($data[$t][$s]['report']['to'] / $data[$t][$s]['report']['wh'] , 1);
		}

		//logistic efficiency
		if($t == 0 && $s == 0 && $data[$t][$s]['report']['prod'] != null){$ut = (80 / 36);  // global UT = avg (ut in & out) = 80 s/to
		}elseif($t == 1 || $t == 4 && $data[$t][$s]['report']['prod'] != null){$ut = (109 / 36);  // Outbound UT = 109 s/to
		}elseif($t == 2 || $t == 5 && $data[$t][$s]['report']['prod'] != null){$ut = (48 / 36);  // Inbound UT = 48 s/to
		}else{$ut = 0;}  // No UT
		$data[$t][$s]['report']['le'] = round($data[$t][$s]['report']['prod'] * $ut,1) ; 

		foreach ($u->getInputEntries() as $e) {
	    	foreach ($e->getActivityHours() as $a) {
	        	$k = array_search($a->getActivity()->getName(), $data[$t][$s]['activities']['cat']);
	        	if($k === false){
	            	$data[$t][$s]['activities']['cat'][] = $a->getActivity()->getName();
	            	$k = array_search($a->getActivity()->getName(), $data[$t][$s]['activities']['cat']);
	          	}
	          	if(array_key_exists($k, $data[$t][$s]['activities']['data'])){
	            	$data[$t][$s]['activities']['data'][$k] += $a->getRegularHours() + $a->getOtHours();
	          	}else{
	            	$data[$t][$s]['activities']['data'][$k] = $a->getRegularHours() + $a->getOtHours();
	          	}       
	        }
	    }

	    $em = $this->getDoctrine()->getManager();
	    for ($i=0; $i < sizeof($data[$t][$s]['activities']['cat']); $i++) { 
	    	//load/refresh ke
	    	$tar = $em->getRepository('SEInputBundle:Activity')->findOneBy(array('name' => $data[$t][$s]['activities']['cat'][$i]));
			$target = intval($tar->getDefaultTarget());
          	if($target != null && $target != 0 && $data[$t][$s]['activities']['data'][$i] != null && $data[$t][$s]['activities']['data'][$i] != 0){
          		$data[$t][$s]['activities']['ke'][$i] = round( $data[$t][$s]['report']['to'] / ( $target * $data[$t][$s]['activities']['data'][$i] ) * 100, 1 );
          	}else{
          		$data[$t][$s]['activities']['ke'][$i] = null;
          	}
	    }

		$data[$t][$s]['to'][$d] += $u->getTotalToInput();
		$data[$t][$s]['h'][$d] += $u->getTotalHoursInput();
		$data[$t][$s]['wh'][$d] += $u->getTotalWorkingHoursInput();

		if($data[$t][$s]['wh'][$d] != 0){
			$data[$t][$s]['prod'][$d] = round($data[$t][$s]['to'][$d] / $data[$t][$s]['wh'][$d] , 1);
		}

		return $data;
	}

	public function loadDailyData($data, $t, $s, $u)
	{
		$data[$t][$s]['report']['to'] += $u->getTotalToInput();
		$data[$t][$s]['report']['mh'] += $u->getTotalHoursInput(); 
		$data[$t][$s]['report']['wh'] += $u->getTotalWorkingHoursInput(); 
		$data[$t][$s]['report']['mto'] += $u->getManualTo(); 
		$data[$t][$s]['report']['tr'] += $u->getTotalTrainingHours(); 
		$data[$t][$s]['report']['ab'] += $u->getTotalAbsence(); 
		$data[$t][$s]['report']['hc'] += $u->getTotalHeadcount();
		$data[$t][$s]['report']['ot'] += $u->getTotalOvertimeInput();
		if($data[$t][$s]['report']['wh'] != 0){
			$data[$t][$s]['report']['prod'] = round($data[$t][$s]['report']['to'] / $data[$t][$s]['report']['wh'] , 1);
		}
		//logistic efficiency
		if($t == 0 && $s == 0 && $data[$t][$s]['report']['prod'] != null){$ut = (80 / 36);  // global UT = avg (ut in & out) = 80 s/to
		}elseif($t == 1 || $t == 4 && $data[$t][$s]['report']['prod'] != null){$ut = (109 / 36);  // Outbound UT = 109 s/to
		}elseif($t == 2 || $t == 5 && $data[$t][$s]['report']['prod'] != null){$ut = (48 / 36);  // Inbound UT = 48 s/to
		}else{$ut = 0;}  // No UT
		$data[$t][$s]['report']['le'] = round($data[$t][$s]['report']['prod'] * $ut,1) ;

		foreach ($u->getInputEntries() as $e) {
	    	foreach ($e->getActivityHours() as $a) {
	        	$k = array_search($a->getActivity()->getName(), $data[$t][$s]['activities']['cat']);
	        	if($k === false){
	            	$data[$t][$s]['activities']['cat'][] = $a->getActivity()->getName();
	            	$k = array_search($a->getActivity()->getName(), $data[$t][$s]['activities']['cat']);
	          	}
	          	if(array_key_exists($k, $data[$t][$s]['activities']['data'])){
	            	$data[$t][$s]['activities']['data'][$k] += $a->getRegularHours() + $a->getOtHours();
	          	}else{
	            	$data[$t][$s]['activities']['data'][$k] = $a->getRegularHours() + $a->getOtHours();
	          	}   
	        }
	    }

	    $em = $this->getDoctrine()->getManager();
	    for ($i=0; $i < sizeof($data[$t][$s]['activities']['cat']); $i++) { 
	    	//load/refresh ke
	    	$tar = $em->getRepository('SEInputBundle:Activity')->findOneBy(array('name' => $data[$t][$s]['activities']['cat'][$i]));
			$target = intval($tar->getDefaultTarget());
          	if($target != null && $target != 0 && $data[$t][$s]['activities']['data'][$i] != null && $data[$t][$s]['activities']['data'][$i] != 0){
          		$data[$t][$s]['activities']['ke'][$i] = round( $data[$t][$s]['report']['to'] / ( $target * $data[$t][$s]['activities']['data'][$i] ) * 100, 1 );
          	}else{
          		$data[$t][$s]['activities']['ke'][$i] = null;
          	}
	    }

		return $data;
	}

	public function createJson($structure)
	{
		$em = $this->getDoctrine()->getManager();
		$teams = $em->getRepository('SEInputBundle:Team')->getReportingTeams();
		$json = array();
		$json[0][0] = $structure;

		foreach ($teams as $team) {
			$teamId = $team->getId();
			if ($teamId != 3){//ignore local structure
				$json[$teamId][0] = $structure;
				for ($i=0; $i < $team->getShiftnb(); $i++) { 
					$shiftId = $i+1;
					$json[$teamId][$shiftId] = $structure;
				}
			}
		}
		return $json;
	}

	public function forgetKeys($data){

		for ($t=0; $t < 10; $t++) { 
			for ($s=0; $s < 4; $s++) { 
				if(isset( $data[$t][$s] , $data )){
					$data[$t][$s]['prod'] = array_values( $data[$t][$s]['prod'] );
					$data[$t][$s]['h'] = array_values( $data[$t][$s]['h'] );
					$data[$t][$s]['wh'] = array_values( $data[$t][$s]['wh'] );
					$data[$t][$s]['to'] = array_values( $data[$t][$s]['to'] );
				}
			}
		}
		return $data;
	}
}