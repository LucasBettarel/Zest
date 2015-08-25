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
	   
	    return $this->render('SEReportBundle:Productivity:hr.html.twig', array(
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
}