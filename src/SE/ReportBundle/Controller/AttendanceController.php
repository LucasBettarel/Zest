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

class AttendanceController extends Controller
{
	public function hrAction()
	{ 
	    $em = $this->getDoctrine()->getManager();
   		$employees = $em->getRepository('SEInputBundle:Employee')->getAlphaEmployees();
   		$year = date('Y');
   		$month = date('n');
   		$calendar = $em->getRepository('SEReportBundle:Calendar')->getMonth($month, $year);
	   
	    return $this->render('SEReportBundle:Attendance:hr.html.twig', array(
    		'calendar' => $calendar,
    		'employees' => $employees
    	)); 
	}

	public function attendanceAction()
	{ 
	    $em = $this->getDoctrine()->getManager();
	    $request = $this->get('request');        
	    $year = $request->get('year');
	    $month = $request->get('month');
	    $daysNb = cal_days_in_month(CAL_GREGORIAN, $month, $year);
	    $jsonAttendance = array('days' => array(), 'hub' => array());
	    $dAttendance = array();
	    $structure = array('rate' => 0, 'hc' => 0, 'hr' => 0, 'ot' => 0, 'not' => 0, 'phot' => 0, 'cons' => 0,	'mh' => 0, 'me' => 0);
	    $structure2 = array('presence' => 0, 'absence' => 0, 'tothr' => 0, 'reghr' => 0, 'othr' => 0);
	    $jsonAttendance['hub'] = $structure;
	    for ($i=0; $i < $daysNb; $i++) {$jsonAttendance['days'][] = $i+1;}
	    
	    $teams = $em->getRepository('SEInputBundle:Team')->findAll();
		//$shifts = $em->getRepository('SEInputBundle:Shift')->findAll();
		$userInputs = $em->getRepository('SEInputBundle:UserInput')->getMonthInputs($month,$year);
   		$employees = $em->getRepository('SEInputBundle:Employee')->getAlphaEmployees();


		//create employee-array structure
		foreach ($employees as $employee) {
			$dAttendance[$employee->getId()] = array('name' => $employee->getName(), 'sesa' => $employee->getSesa());
			for ($i=0; $i < $daysNb; $i++) { 
				$dAttendance[$employee->getId()][($i+1)] = $structure2;
			}
		}

		//create filter-array structure
		//works only if number of teams < 10
		foreach ($teams as $team) {
			$name = $team->getId();
			$jsonAttendance[$name] = $structure;
			if($team->getShiftnb() > 1) {
				for ($i=0; $i < $team->getShiftnb(); $i++) { 
					$sname = $name.($i+1);
					$jsonAttendance[$sname] = $structure;
				}
			}
		}

		//fill data
		foreach ($userInputs as $userInput) {
			$teamShift = $userInput->getTeam()->getId().$userInput->getShift()->getId();
			foreach ($userInput->getInputEntries() as $inputEntry) {
				//$jsonAttendance = loadjAttData($teamShift, $inputEntry, $jsonAttendance);
				//$dAttendance = loaddAttData($teamShift, $inputEntry, $dAttendance);
			}
		}
		

		$response = array("code" => 100, "success" => true, "jsonAttendance" => $jsonAttendance, "dAttendance" => $dAttendance, "teamShift" => $teamShift);
	    
	    return new Response(json_encode($response)); 
	}

	public function loadjAttData($ts, $i, $j){

		

		$key = array_search($a->getActivity()->getName(), $jsonActivities['cat']);
         if($key === false){
            $jsonActivities['cat'][] = $a->getActivity()->getName();
            $key = array_search($a->getActivity()->getName(), $jsonActivities['cat']);
          }
          if(array_key_exists($key, $jsonActivities['data'])){
            $jsonActivities['data'][$key] += $a->getRegularHours() + $a->getOtHours();
          }else{
            $jsonActivities['data'][$key] = $a->getRegularHours() + $a->getOtHours();
          }            

        return $j;
	}

	public function loaddAttData($ts, $i, $d){
		return $d;
	}

	public function employeesAttendanceAction()
	{ 
	    $em = $this->getDoctrine()->getManager();
	    $request = $this->get('request');        
	    $year = $request->get('year');
	    $month = $request->get('month');
	    $daysNb = cal_days_in_month(CAL_GREGORIAN, $month, $year);
	    $hourStructure = array('presence' => 0, 'absence' => 0, 'tothr' => 0, 'reghr' => 0, 'othr' => 0);
	    //$infoStructure = array('id' => 0, 'name' => 0, 'sesa' => 0, 'dTeam' => 0, 'dShift' => 0, 'dActivity' => 0);	
		$userInputs = $em->getRepository('SEInputBundle:UserInput')->getMonthInputs($month,$year);
   		$employees = $em->getRepository('SEInputBundle:Employee')->getAlphaEmployees();
   		$jsonAttendance = array();

		//create employee-array structure
		foreach ($employees as $employee) {
			//create html name cell
			$eCell = "<div id='".$employee->getId()."' class='e-name' data-toggle='tooltip' data-placement='right' title='<strong>".$employee->getDefaultTeam()->getName()." - Shift ".$employee->getDefaultShift()->getId()."</strong><br>".$employee->getSesa()."<br>".$employee->getDefaultActivity()->getName()."'>".$employee->getName()."</div>";
			$jsonAttendance[$employee->getId()] = array('employee' => $eCell, 'team' => $employee->getDefaultTeam()->getName(), 'shift' => $employee->getDefaultShift()->getId(), 'total'=>0);
			for ($i=0; $i < $daysNb; $i++) { 
				$jsonAttendance[$employee->getId()][($i+1)] = $hourStructure;
			}
		}

		//fill data
		foreach ($userInputs as $userInput) {
			$dateInput = $userInput->getDateInput()->format("j");
			//when details of activities in tooltip -> team+shift required here
			foreach ($userInput->getInputEntries() as $inputEntry) {
				$regtohr = 0;
				$ottohr = 0;
				$employeeId = $inputEntry->getEmployee()->getId();
				if($inputEntry->getPresent()){
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

		//calcul monthly total and formate to html
		for ($j=1; $j < sizeof($jsonAttendance); $j++) { 
		 	if(isset($jsonAttendance[$j])){
			 		for ($i=1; $i <= $daysNb; $i++) { 
					$jsonAttendance[$j]['total'] += $jsonAttendance[$j][$i]['tothr'];
					//test to identify errors in hours reported
					if($jsonAttendance[$j][$i]['presence'] == 0){
						if($jsonAttendance[$j][$i]['absence'] != 0){//leave
							$dCell = "<div id='".$i."' class='e-day-leave'><i class='glyphicon glyphicon-error'> </i> ".$jsonAttendance[$j][$i]['absence']."</div>";
						}else{//not working
							$dCell = "<div id='".$i."' class='e-day-absent'></div>";	
						}
					}elseif($jsonAttendance[$j][$i]['tothr'] > 11 || $jsonAttendance[$j][$i]['othr'] > 8 || $jsonAttendance[$j][$i]['reghr'] > 8){//warning too much hour
						$dCell = "<div id='".$i."' class='e-day-high'><i class='glyphicon glyphicon-exclamation-sign'> </i> ".$jsonAttendance[$j][$i]['tothr']."</div>";
					}elseif( $jsonAttendance[$j][$i]['reghr'] < 7.5 ){//missing hour
						$dCell = "<div id='".$i."' class='e-day-low'><i class='glyphicon glyphicon-exclamation-sign'> </i> ".$jsonAttendance[$j][$i]['tothr']."</div>";					
					}else{//ok
						$dCell = "<div id='".$i."' class='e-day-ok'><i class='glyphicon glyphicon-ok'> </i> ".$jsonAttendance[$j][$i]['tothr']."</div>";					
					}
					$jsonAttendance[$j][$i] = $dCell; 
				}
			}
		}
		
		$response = array("code" => 100, "success" => true, "jsonAttendance" => $jsonAttendance);
	    
	    return new Response(json_encode($response)); 
	}

	public function createJson($structure)
	{
		$em = $this->getDoctrine()->getManager();
		$teams = $em->getRepository('SEInputBundle:Team')->findAll();
		$json = array();
		$json[0][0] = $structure;

		foreach ($teams as $team) {
			$teamId = $team->getId();
			$json[$teamId][0] = $structure;
			for ($i=0; $i < $team->getShiftnb(); $i++) { 
				$shiftId = $i+1;
				$json[$teamId][$shiftId] = $structure;
			}
		}
		return $json;
	}
}