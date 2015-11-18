<?php

namespace SE\ReportBundle\Refresher;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Routing\RouterInterface;
use SE\ReportBundle\Entity\AttendanceData;
use SE\InputBundle\Entity\UserInput;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class AttendanceRefresher
{
	protected $em;

    public function __construct(EntityManager $em, Structurer $structurer, RouterInterface $router)
    {
        $this->em = $em;
        $this->structurer = $structurer;
        $this->router = $router;
    }

	public function updateData($year, $month, $days)
	{ 
	    $jsonAttendance = $this->structurer->getAttendanceStructure($year, $month, sizeof($days));
	    $jsonAttendance = $this->fillData($jsonAttendance, $month, $year);

	    $format = $this->formatData($jsonAttendance, $days, $month, $year);
	    $template = $format['template'];
	    $jsonAttendance = $format['jsonAttendance'];

	    $jsonData = $this->getTotalData($jsonAttendance, $days);

		$res = $this->saveData($year, $month, $jsonAttendance, $jsonData, $template);
		
		$response = array("jsonAttendance" => $jsonAttendance,  // NO NEED TO SEND
						  "template" => $template,
						  "jsonData" => $jsonData
						);
	    
	    return $response; 
	}

	public function fillData($jsonAttendance, $month, $year){

		//TODO : Maybe faster with query on inputEntries directly
		$userInputs = $this->em->getRepository('SEInputBundle:UserInput')->getMonthInputs($month,$year);
   		
		foreach ($userInputs as $userInput) {
			$dateInput = $userInput->getDateInput()->format("j");
			//when details of activities in tooltip -> team+shift required here
			foreach ($userInput->getInputEntries() as $inputEntry) {
				$regtohr = 0;
				$ottohr = 0;
				$employeeId = $inputEntry->getEmployee()->getMasterId();
				if($inputEntry->getPresent() == 1 ){
					//go into activities to remove this fucking transfer out mess...
					//TODO : update when this transfer out fuck will be better
					if($inputEntry->getActivityHours()){
						foreach ($inputEntry->getActivityHours() as $activityHour) {
						 	if($activityHour->getActivity()->getId() == 13){
						 		$regtohr = $activityHour->getRegularHours();
						 		$ottohr = $activityHour->getOtHours();
						 		break;
						 	}
						}
					}
					//update hours
					$jsonAttendance[$employeeId][$dateInput]['presence'] = 1;
					$jsonAttendance[$employeeId][$dateInput]['absence'] = 0;
					$jsonAttendance[$employeeId][$dateInput]['othr'] += $inputEntry->getTotalOvertime() - $ottohr;
					$jsonAttendance[$employeeId][$dateInput]['reghr'] += $inputEntry->getTotalHours() - $inputEntry->getTotalOvertime() - $regtohr;
					$jsonAttendance[$employeeId][$dateInput]['tothr'] += $inputEntry->getTotalHours() - $ottohr - $regtohr;
				}else{
					$jsonAttendance[$employeeId][$dateInput]['presence'] = 0;
					//TODO : Update when halfdays will be managed
					$jsonAttendance[$employeeId][$dateInput]['absence'] = $inputEntry->getAbsenceReason()->getName();
				}
			}
		}
	
		return $jsonAttendance;
	}

	public function formatData($att, $days, $month, $year){

		$template = array();
		$templateRow = array();
    	
    	//TODO : Use foreach like in gettotal data for employees
		foreach ($att as $j => $e) {
   			if(isset($e)){
		 		unset($templateRow);
		 		$templateRow = array();  
		 		$templateRow[] = $e['employee'];
		 		$templateRow[] = $e['team'];
		 		$templateRow[] = $e['shift'];
			 	foreach ($days as $i => $day) {
   					$i++; //offset is 1->dayNb on jsonAttendance
					$e['total'] += $e[$i]['tothr'];
					//test to identify errors in hours reported
					if($e[$i]['presence'] == 0){
						if(isset($e[$i]['absence']) && $e[$i]['absence'] != "0"){//leave
							$dCell = "<div data-d='".$i."' data-m='".$month."' data-y='".$year."' data-e='".$j."' class='e-day-leave'><i class='glyphicon glyphicon-remove'> </i> <strong>".$e[$i]['absence']."</strong></div>";
						}else{//not working
							$dCell = "<div data-d='".$i."' data-m='".$month."' data-y='".$year."' data-e='".$j."' class='e-day-absent'></div>";	
						}
					}elseif($e[$i]['tothr'] > 11 || $e[$i]['othr'] > 8 || $e[$i]['reghr'] > 8){//warning too much hour
						$dCell = "<div data-d='".$i."' data-m='".$month."' data-y='".$year."' data-e='".$j."' class='e-day-high'><i class='glyphicon glyphicon-exclamation-sign'> </i> ".$e[$i]['tothr']."</div>";
					}elseif( ( $e[$i]['reghr'] < 8 && $day['isWeekday'] ) || $e[$i]['tothr'] == 0 ){//missing hour
						$dCell = "<div data-d='".$i."' data-m='".$month."' data-y='".$year."' data-e='".$j."' class='e-day-low'><i class='glyphicon glyphicon-exclamation-sign'> </i> ".$e[$i]['tothr']."</div>";					
					}else{//ok
						$dCell = "<div data-d='".$i."' data-m='".$month."' data-y='".$year."' data-e='".$j."' class='e-day-ok'><i class='glyphicon glyphicon-ok'> </i> ".$e[$i]['tothr']."</div>";					
					}
					$templateRow[] = $dCell;
				}
				$templateRow[] = $e['total'];
				if($e['total'] > 0){ //if no hours during the month, do not include in table
					$template[] = $templateRow;
				}
			}
		}

		return array('jsonAttendance' => $att, 'template' => $template);
	}

	public function getTotalData($att, $days){

		$data = $this->structurer->getAttendanceReportStructure($year, $month);

   		foreach ($days as $d => $day) {
   			$d++; //offset is 1->dayNb on jsonAttendance
   			foreach ($att as $id => $e) {
   				if(isset($e) && isset($e[$d])){
   					//presence
	   				if( $e[$d]['presence'] == 1 && $e[$d]['tothr'] > 0 ){
	   					$data[0][0]['report']['presence'] += 1;
	   					$data[$e['teamId']][0]['report']['presence'] += 1;
	   					$data[$e['teamId']][$e['shift']]['report']['presence'] += 1;
	   				}

	   				//total headcount
	   				if( ( $e[$d]['presence'] == 1  && $e[$d]['tothr'] > 0 ) || ( $e[$d]['presence'] == 0  && isset($e[$d]['absence']) && $e[$d]['absence'] != "0" ) ){
	   					$data[0][0]['report']['hc'] += 1;
	   					$data[$e['teamId']][0]['report']['hc'] += 1;
	   					$data[$e['teamId']][$e['shift']]['report']['hc'] += 1;
	   				}

	   				//regular hours
	   				$data[0][0]['report']['totalreghr'] += $e[$d]['reghr'];
	   				$data[$e['teamId']][0]['report']['totalreghr'] += $e[$d]['reghr'];
	   				$data[$e['teamId']][$e['shift']]['report']['totalreghr'] += $e[$d]['reghr'];

					//ot hours
					$data[0][0]['report']['totalothr'] += $e[$d]['othr'];
					$data[$e['teamId']][0]['report']['totalothr'] += $e[$d]['othr'];
					$data[$e['teamId']][$e['shift']]['report']['totalothr'] += $e[$d]['othr'];

					//total hours
					$data[0][0]['report']['totalhr'] += $e[$d]['tothr'];
					$data[$e['teamId']][0]['report']['totalhr'] += $e[$d]['tothr'];
					$data[$e['teamId']][$e['shift']]['report']['totalhr'] += $e[$d]['tothr'];


					if ( $day['isWeekday'] && !$day['isHoliday'] ){
						$data[0][0]['report']['wdot'] += $e[$d]['othr'];
						$data[$e['teamId']][0]['report']['wdot'] += $e[$d]['othr'];
						$data['report']['wdot'] += $e[$d]['othr'];
					}else{
						$data[0][0]['report']['weot'] += $e[$d]['othr'];
						$data[$e['teamId']][0]['report']['weot'] += $e[$d]['othr'];
						$data[$e['teamId']][$e['shift']]['report']['weot'] += $e[$d]['othr'];
					}
   				}
   			}
   		}

		if(	$data['report']['hc'] > 0){ $data['report']['attrate'] = 100 * round( $data['report']['presence'] / $data['report']['hc'] , 2 );}   		

		return $data;					
	}

	public function getTotalData($att, $days){

		if( $e[$d]['presence'] == 1 && $e[$d]['tothr'] > 0 ){$data['report']['presence'] += 1;}
		if( ( $e[$d]['presence'] == 1  && $e[$d]['tothr'] > 0 ) || ( $e[$d]['presence'] == 0  && isset($e[$d]['absence']) && $e[$d]['absence'] != "0" ) ){ $data['report']['hc'] += 1; }
		$data['report']['totalreghr'] += $e[$d]['reghr'];
		$data['report']['totalothr'] += $e[$d]['othr'];
		$data['report']['totalhr'] += $e[$d]['tothr'];
		if ( $day['isWeekday'] && !$day['isHoliday'] ){$data['report']['wdot'] += $e[$d]['othr'];}
		else{$data['report']['weot'] += $e[$d]['othr'];}

		return $data;					
	}

	public function saveData($year, $month, $jsonAttendance, $jsonData, $template)
	{ 
		//TODO : error handler
		$data = $this->em->getRepository('SEReportBundle:AttendanceData')->findOneBy(array('year' => $year, 'month' => $month));

		if($data){
			$data->setJsonAttendance(json_encode($jsonAttendance));
			$data->setJsonData(json_encode($jsonData));
			$data->setTableTemplate(json_encode($template));
			$data->setRefresher(0);
			
			//$this->em->persist($data);
		}else{
			$newData = new AttendanceData();

			$newData->setMonth($month);
			$newData->setYear($year);
			$newData->setJsonAttendance(json_encode($jsonAttendance));
			$newData->setJsonData(json_encode($jsonData));
			$newData->setTableTemplate(json_encode($template));

			$this->em->persist($newData);
		}
		
		$this->em->flush();

		return true;
	}

	public function getActivityDetails($y, $m, $d, $e){

	    $is = $this->em->getRepository('SEInputBundle:InputEntry')->getEmployeeInputsAtDate($d,$m,$y,$e);
	    $det = array('totot' => 0, 'totreg' => 0, 'tothr' => 0, 'to' => 0, 'res' => 0, 'whr' => 0);
	    $j = 0;

	    foreach ($is as $i) {
			$regtohr = 0;
			$ottohr = 0;
	    	$det['name'] = $i->getEmployee()->getName();
	    	$det['date'] = $i->getUserInput()->getDateInput()->format('d-m-Y');
	    	$det['tab'][$j]['present'] = $i->getPresent();
	    	if( !$det['tab'][$j]['present'] ){ $det['tab'][$j]['absence'] = $i->getAbsenceReason()->getName(); }
	    	$det['tab'][$j]['header'] = $i->getUserInput()->getTeam()->getName()." - Shift ".$i->getUserInput()->getShift()->getId()." - ".$i->getSesa();
	    	$det['tab'][$j]['link'] = $this->router->generate('se_input_review_details', array('id' => $i->getUserInput()->getId()));
	    	if( $i->getActivityHours() ){
	    		foreach ($i->getActivityHours() as $a) {
	    			if($a->getActivity()->getId() == 13){
				 		$regtohr = $a->getRegularHours();
				 		$ottohr = $a->getOtHours();
					}else{
						$det['tab'][$j]['row'][] = "<tr><td>".$a->getActivity()->getName()."</td><td>".$a->getRegularHours()."</td><td>".$a->getOtHours()."</td></tr>";
					}	
		    	}
		    }
	    	$det['totot'] += $i->getTotalOvertime() - $ottohr;
	    	$det['totreg'] += $i->getTotalHours() - $i->getTotalOvertime() - $regtohr;;
	    	$det['tothr'] += $i->getTotalHours() - $ottohr - $regtohr;
	    	$det['to'] += $i->getTotalTo();
	    	$det['whr'] += $i->getTotalWorkingHours();
	    	if ( $det['whr'] != 0 ){ $det['res'] = round( $det['to'] / $det['whr'] , 2); }
	    	$j++;
	    }

		return $det;
	}
}
