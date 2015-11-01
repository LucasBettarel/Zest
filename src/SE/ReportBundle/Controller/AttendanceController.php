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
   		$year = date('Y');
   		$month = date('n');
   		$calendar = $em->getRepository('SEReportBundle:Calendar')->getMonth($month, $year);
	   
	    return $this->render('SEReportBundle:Attendance:hr.html.twig', array(
    		'calendar' => $calendar
    	)); 
	}

	public function attendanceAction()
	{ 
	    $em = $this->getDoctrine()->getManager();
	    $request = $this->get('request');        
	    $year = $request->get('year');
	    $month = $request->get('month');
	    $daysNb = cal_days_in_month(CAL_GREGORIAN, $month, $year);
	    $hourStructure = array('presence' => 0, 'absence' => 0, 'tothr' => 0, 'reghr' => 0, 'othr' => 0);
	    $userInputs = $em->getRepository('SEInputBundle:UserInput')->getMonthInputs($month,$year);
   		$employees = $em->getRepository('SEInputBundle:Employee')->getHistoricalEmployees($year, $month);
   		$jsonAttendance = array();

		//create employee-array structure
		foreach ($employees as $employee) {
			//create html name cell
			$eCell = "<div id='".$employee->getMasterId()."' class='e-name' data-toggle='tooltip' data-placement='right' title='<strong>".$employee->getDefaultTeam()->getName()." - Shift ".$employee->getDefaultShift()->getId()."</strong><br>".$employee->getSesa()."<br>".$employee->getDefaultActivity()->getName()."'>".$employee->getName()."</div>";
			$jsonAttendance[$employee->getMasterId()] = array('employee' => $eCell, 'team' => $employee->getDefaultTeam()->getName(), 'shift' => $employee->getDefaultShift()->getId(), 'total'=>0);
			for ($i=0; $i < $daysNb; $i++) { 
				$jsonAttendance[$employee->getMasterId()][($i+1)] = $hourStructure;
			}
		}

		//fill data
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

		$template = array();
		$templateRow = array();

		//calcul monthly total and formate to html
		$lastKey = $em->getRepository('SEInputBundle:Employee')->getMaxId();
		for ($j=1; $j <= intval($lastKey[0][1]); $j++) { 
		 	if(isset($jsonAttendance[$j])){
		 		unset($templateRow);
		 		$templateRow = array();  
		 		$templateRow[] = $jsonAttendance[$j]['employee'];
		 		$templateRow[] = $jsonAttendance[$j]['team'];
		 		$templateRow[] = $jsonAttendance[$j]['shift'];
			 	for ($i=1; $i <= $daysNb; $i++) { 
			 		$day = $em->getRepository('SEReportBundle:Calendar')->findOneBy(array('y' => $year, 'm' => $month, 'd' => $i));
					$jsonAttendance[$j]['total'] += $jsonAttendance[$j][$i]['tothr'];
					//test to identify errors in hours reported
					if($jsonAttendance[$j][$i]['presence'] == 0){
						if(isset($jsonAttendance[$j][$i]['absence']) && $jsonAttendance[$j][$i]['absence'] != "0"){//leave
							$dCell = "<div id='".$i."' class='e-day-leave'><i class='glyphicon glyphicon-remove'> </i> <strong>".$jsonAttendance[$j][$i]['absence']."</strong></div>";
						}else{//not working
							$dCell = "<div id='".$i."' class='e-day-absent'></div>";	
						}
					}elseif($jsonAttendance[$j][$i]['tothr'] > 11 || $jsonAttendance[$j][$i]['othr'] > 8 || $jsonAttendance[$j][$i]['reghr'] > 8){//warning too much hour
						$dCell = "<div id='".$i."' class='e-day-high'><i class='glyphicon glyphicon-exclamation-sign'> </i> ".$jsonAttendance[$j][$i]['tothr']."</div>";
					}elseif( ( $jsonAttendance[$j][$i]['reghr'] < 8 && $day->getIsWeekday() ) || $jsonAttendance[$j][$i]['tothr'] == 0 ){//missing hour
						$dCell = "<div id='".$i."' class='e-day-low'><i class='glyphicon glyphicon-exclamation-sign'> </i> ".$jsonAttendance[$j][$i]['tothr']."</div>";					
					}else{//ok
						$dCell = "<div id='".$i."' class='e-day-ok'><i class='glyphicon glyphicon-ok'> </i> ".$jsonAttendance[$j][$i]['tothr']."</div>";					
					}
					$jsonAttendance[$j][$i] = $dCell; 
					$templateRow[] = $jsonAttendance[$j][$i];
				}
				$templateRow[] = $jsonAttendance[$j]['total'];
				if($jsonAttendance[$j]['total'] > 0){ //if no hours during the month, do not include in table
					$template[] = $templateRow;
				}
			}
		}
		
		$response = array("code" => 100, "success" => true, "jsonAttendance" => $jsonAttendance, "template" => $template);
	    
	    return new Response(json_encode($response)); 
	}
}