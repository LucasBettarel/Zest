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
		$dp = $em->getRepository('SEInputBundle:Departement')->getCurrentDepartements();

 		return $this->render('SEReportBundle:Productivity:monthly.html.twig', array('dp' => $dp));
	}

	public function dailyAction()
	{
		$em = $this->getDoctrine()->getManager();
		$dp = $em->getRepository('SEInputBundle:Departement')->getCurrentDepartements();

 		return $this->render('SEReportBundle:Productivity:daily.html.twig', array('dp' => $dp));
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
    		$start = $inputShift->getStartTime();
	        $end = $inputShift->getEndTime();
    		$otStart = $inputToProcessDay->getOtStartTime();
			$otEnd = $inputToProcessDay->getOtEndTime();
			$regularReverse = ($start < $end ? true : false);
			$otReverse = ($otStart < $otEnd ? true : false);
					         		
        	foreach ($sapToProcess as $sapToProcessDay) {
      
	        	if($inputDate->format("Y-m-d") == $sapToProcessDay->getDate()->format("Y-m-d")){
	        		//match: input.date present in sap.date
	        		foreach ($inputToProcessDay->getInputEntries() as $inputEntry) {
	        			foreach ($inputEntry->getActivityHours() as $activity) {
	        				//picking or putaway
	        				if ($activity->getActivity()->getTrackable() == true){
	        					$sesa = $inputEntry->getSesa();
	        					$to = $inputEntry->getTotalTo();
	
						 		//go in saprf and do the shit.
        					    $TOlines = $em->getRepository('SEInputBundle:SAPRF')->getTo($inputDate, $sesa);
							
								//restrict by hours
								foreach ($TOlines as $line) {
									$timeConf = $line->getTimeConfirmation(); 
									//if inside right time interval + to line not already affected
									if($line->getRecorded() == 0){ 
										if( ( $regularReverse && ($timeConf <= $end) && ($timeConf >= $start) ) or ( !$regularReverse && ( ( $timeConf >= $start ) or ( $timeConf <= $end ) ) ) ) { //regular hours
											if( ( $line->getSourceStorageType() == '902' || $line->getSourceStorageType() == '901' || $line->getSourceStorageType() == 'X04' ) && $activity->getActivity()->getId() == 3){ //putaway
												$to += 1; //ok
												$line->setRecorded(1);
											}elseif( $line->getSourceStorageType() != '902' && $line->getSourceStorageType() != '901' && $activity->getActivity()->getId() == 2 ){//picking
												$to += 1; //ok
												$line->setRecorded(1);
											}else{
												$missingTO += 1; //pas ok
											}	
										}
										elseif ( ( ( $otReverse && ($timeConf <= $otEnd) && ($timeConf >= $otStart) ) or ( !$otReverse && ( ( $timeConf >= $otStart ) or ( $timeConf <= $otEnd ) ) ) ) && $activity->getOtHours() > 0 ) { //overtime 
											if( ( $line->getSourceStorageType() == '902' || $line->getSourceStorageType() == '901' || $line->getSourceStorageType() == 'X04' ) && $activity->getActivity()->getId() == 3){ //putaway
												$to += 1; //ok
												$line->setRecorded(1);
											}elseif( $line->getSourceStorageType() != '902' && $line->getSourceStorageType() != '901' && $activity->getActivity()->getId() == 2 ){//picking
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
	        		$manualTOlines = $em->getRepository('SEInputBundle:SAPRF')->getGeneralManualTo($inputDate, $inputTeam->getMasterId());

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
	    $departements = $em->getRepository('SEInputBundle:Departement')->getHistoricalDepartements($year,$month);
	    $filters = $this->render('SEReportBundle:Utilities:filters.html.twig', array('dp' => $departements))->getContent();
	    $daysNb = cal_days_in_month(CAL_GREGORIAN, $month, $year);
	    $userInputs = $em->getRepository('SEInputBundle:UserInput')->getMonthInputs($month,$year);
	    $monthlyStructure = array(
						'report' => array('prod' => 0, 'to' => 0,'mh' => 0,'hc' => 0,'tr' => 0,'ab' => 0,'ot' => 0,'wh' => 0, 'mto' => 0, 'le' => 0, 'ksr' => 0),
						'activities' => array('cat' => array(), 'data' => array(), 'ke' => array()),
						'prod' => array()
						);
	    for ($i=1; $i <= $daysNb; $i++) {
	    	$monthlyStructure['prod'][$i]['y'] = 0;
	    	$monthlyStructure['prod'][$i]['tip'][1] = 0;
	    	$monthlyStructure['prod'][$i]['tip'][2] = 0;
	    	$monthlyStructure['prod'][$i]['tip'][3] = 0;
	    }
	    
	    $monthlyJson = $this->createJson($monthlyStructure, $departements);
	    for ($i=1; $i <= $daysNb; $i++) {$monthlyJson['days'][] = $i;}

		//fill data
		foreach ($userInputs as $userInput) {
			$day = $userInput->getDateInput()->format('j');
			$dep = $userInput->getTeam()->getDepartement();
			$shift = $userInput->getShift()->getId();
			$monthlyJson = $this->loadMonthlyData($monthlyJson, $dep, $shift, $day, $userInput);
			$monthlyJson = $this->loadMonthlyData($monthlyJson, $dep, 0, $day, $userInput);
			$monthlyJson = $this->loadMonthlyData($monthlyJson, 0, 0, $day, $userInput);
		}

		$monthlyJson = $this->forgetKeys($monthlyJson);

		$response = array("code" => 100, "success" => true, "monthlyJson" => $monthlyJson, "filters" => $filters);
	    
	    return new Response(json_encode($response)); 
	}

	public function dayAction()
	{ 
	    $em = $this->getDoctrine()->getManager();
	    $request = $this->get('request');        
	    $date = $request->get('date');
	    $userInputs = $em->getRepository('SEInputBundle:UserInput')->getDayInputs($date);
	    $departements = $em->getRepository('SEInputBundle:Departement')->getHistoricalDepartements(substr($date, -10, 4), substr($date, -5, 2));
	    $filters = $this->render('SEReportBundle:Utilities:filters.html.twig', array('dp' => $departements))->getContent();
	    $dailyStructure = array(
							'report' => array('prod' => 0, 'to' => 0,'mh' => 0,'hc' => 0,'tr' => 0,'ab' => 0,'ot' => 0,'wh' => 0, 'mto' => 0, 'le' => 0, 'ksr' => 0),
							'activities' => array('cat' => array(), 'data' => array(), 'ke' => array())
						);	    
	    $dailyJson = $this->createJson($dailyStructure, $departements);
	    $template = array();

		//fill data
		foreach ($userInputs as $userInput) {
			$dep = $userInput->getTeam()->getDepartement();
			$shift = $userInput->getShift()->getId();
			$dailyJson = $this->loadDailyData($dailyJson, $dep, $shift, $userInput);
			$dailyJson = $this->loadDailyData($dailyJson, $dep, 0, $userInput);
			$dailyJson = $this->loadDailyData($dailyJson, 0, 0, $userInput);
			$view = $this->render('SEReportBundle:Productivity:dailyTable.html.twig', array('input' => $userInput))->getContent();
			$template[] = array($userInput->getTeam()->getName(),$shift,$userInput->getTotalHoursInput(),$userInput->getTotalToInput(),$view);
		}
		$response = array("code" => 100, "success" => true, "dailyJson" => $dailyJson, "template" => $template, "filters" => $filters);
	    
	    return new Response(json_encode($response)); 
	}

	public function loadMonthlyData($data, $dp, $s, $d, $u)
	{
		$t=is_int($dp)?0:$dp->getMasterId();

		$data[$t][$s]['report']['to'] += $u->getTotalToInput();
		$data[$t][$s]['report']['mh'] += $u->getTotalHoursInput(); 
		$data[$t][$s]['report']['wh'] += $u->getTotalWorkingHoursInput(); 
		$data[$t][$s]['report']['mto'] += $u->getManualTo(); 
		$data[$t][$s]['report']['tr'] += $u->getTotalTrainingHours(); 
		$data[$t][$s]['report']['ab'] += $u->getTotalAbsence(); 
		$data[$t][$s]['report']['hc'] += $u->getTotalHeadcount();
		$data[$t][$s]['report']['ot'] += $u->getTotalOvertimeInput();
		$data[$t][$s]['report']['prod'] = $data[$t][$s]['report']['wh'] != 0 ? round($data[$t][$s]['report']['to'] / $data[$t][$s]['report']['wh'] , 1) : 0;

		//logistic efficiency
		if(is_int($dp)){$ut = (80 / 36);  // global UT = avg (ut in & out) = 80 s/to
		}else{$ut = ( $dp->getLet() / 36 );}
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

	    $support = array_search("Support (Admin, CCMP, WSpider, Labelling, ...)", $data[$t][$s]['activities']['cat']);
	    if ($support !== false && $data[$t][$s]['report']['wh'] != 0){
	    	$data[$t][$s]['report']['ksr'] = round( ( $data[$t][$s]['report']['wh'] - $data[$t][$s]['activities']['data'][$support] )*100 / $data[$t][$s]['report']['wh'] , 1);
	    }

	    $em = $this->getDoctrine()->getManager();
	    for ($i=0; $i < sizeof($data[$t][$s]['activities']['cat']); $i++) { 
	    	//load/refresh ke
	    	$tar = $em->getRepository('SEInputBundle:Activity')->findOneBy(array('name' => $data[$t][$s]['activities']['cat'][$i]));
			$target = intval($tar->getDefaultTarget());
          	if($target != null && $target != 0 && $data[$t][$s]['activities']['data'][$i] != null && $data[$t][$s]['activities']['data'][$i] != 0 && $t != 0){
          		$data[$t][$s]['activities']['ke'][$i] = round( $data[$t][$s]['report']['to'] / ( $target * $data[$t][$s]['activities']['data'][$i] ) * 100, 1 );
          	}else{
          		$data[$t][$s]['activities']['ke'][$i] = null;
          	}
	    }

	    $data[$t][$s]['prod'][$d]['tip'][1] += $u->getTotalHoursInput();
    	$data[$t][$s]['prod'][$d]['tip'][2] += $u->getTotalWorkingHoursInput();
    	$data[$t][$s]['prod'][$d]['tip'][3] += $u->getTotalToInput();

		if($data[$t][$s]['prod'][$d]['tip'][2] != 0){
			$data[$t][$s]['prod'][$d]['y'] = round($data[$t][$s]['prod'][$d]['tip'][3] / $data[$t][$s]['prod'][$d]['tip'][2] , 1);
		}

		return $data;
	}

	public function loadDailyData($data, $dp, $s, $u)
	{
		$t=is_int($dp)?0:$dp->getMasterId();

		$data[$t][$s]['report']['to'] += $u->getTotalToInput();
		$data[$t][$s]['report']['mh'] += $u->getTotalHoursInput(); 
		$data[$t][$s]['report']['wh'] += $u->getTotalWorkingHoursInput(); 
		$data[$t][$s]['report']['mto'] += $u->getManualTo(); 
		$data[$t][$s]['report']['tr'] += $u->getTotalTrainingHours(); 
		$data[$t][$s]['report']['ab'] += $u->getTotalAbsence(); 
		$data[$t][$s]['report']['hc'] += $u->getTotalHeadcount();
		$data[$t][$s]['report']['ot'] += $u->getTotalOvertimeInput();
		$data[$t][$s]['report']['prod'] = $data[$t][$s]['report']['wh'] != 0 ? round($data[$t][$s]['report']['to'] / $data[$t][$s]['report']['wh'] , 1) : 0;

		//logistic efficiency
		if(is_int($dp)){$ut = (80 / 36);  // global UT = avg (ut in & out) = 80 s/to
		}else{$ut = ( $dp->getLet() / 36 );}
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

	    $support = array_search("Support (Admin, CCMP, WSpider, Labelling, ...)", $data[$t][$s]['activities']['cat']);
	    if ($support !== false && $data[$t][$s]['report']['wh'] > 0){
	    	$data[$t][$s]['report']['ksr'] = round( ( $data[$t][$s]['report']['wh'] - $data[$t][$s]['activities']['data'][$support] )*100 / $data[$t][$s]['report']['wh'] , 1);
	    }

	    $em = $this->getDoctrine()->getManager();
	    for ($i=0; $i < sizeof($data[$t][$s]['activities']['cat']); $i++) { 
	    	//load/refresh ke
	    	$tar = $em->getRepository('SEInputBundle:Activity')->findOneBy(array('name' => $data[$t][$s]['activities']['cat'][$i]));
			$target = intval($tar->getDefaultTarget());
          	if($target != null && $target != 0 && $data[$t][$s]['activities']['data'][$i] != null && $data[$t][$s]['activities']['data'][$i] != 0  && $t != 0){
          		$data[$t][$s]['activities']['ke'][$i] = round( $data[$t][$s]['report']['to'] / ( $target * $data[$t][$s]['activities']['data'][$i] ) * 100, 1 );
          	}else{
          		$data[$t][$s]['activities']['ke'][$i] = null;
          	}
	    }

		return $data;
	}

	public function createJson($structure, $deps)
	{
		$em = $this->getDoctrine()->getManager();
		$json = array();
		$json[0][0] = $structure;

		foreach ($deps as $dep) {
			$depId = $dep->getMasterId();
			$json[$depId][0] = 	$structure;
			for ($i=0; $i < $dep->getMaxShiftNb(); $i++) { 
				$shiftId = $i+1;
				$json[$depId][$shiftId] = $structure;
			}
		}
		return $json;
	}

	public function forgetKeys($data){

		for ($t=0; $t < 10; $t++) { 
			for ($s=0; $s < 4; $s++) { 
				if(isset( $data[$t][$s] , $data )){
					foreach ($data[$t][$s]['prod'] as $d) {
						if(isset( $d['tip'])){
							$d['tip'] = array_values( $d['tip'] );
						}
					}
					$data[$t][$s]['prod'] = array_values( $data[$t][$s]['prod'] );
				}
			}
		}
		return $data;
	}
}