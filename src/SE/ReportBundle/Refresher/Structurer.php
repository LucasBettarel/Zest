<?php

namespace SE\ReportBundle\Refresher;

use Doctrine\ORM\EntityManager;

class Structurer
{
	protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

	public function getAttendanceStructure($year, $month, $n)
	{ 
	    $jsonAttendance = array();
   		$hourStructure = array('presence' => 0, 'absence' => 0, 'tothr' => 0, 'reghr' => 0, 'othr' => 0);
   		$employees = $this->em->getRepository('SEInputBundle:Employee')->getHistoricalEmployees($year, $month);

		//create employee-array structure
		foreach ($employees as $employee) {
			//create html name cell
			$eCell = "<div id='".$employee->getMasterId()."' class='e-name' data-toggle='tooltip' data-placement='right' title='<strong>".$employee->getDefaultTeam()->getName()." - Shift ".$employee->getDefaultShift()->getId()."</strong><br>".$employee->getSesa()."<br>".$employee->getDefaultActivity()->getName()."'>".$employee->getName()."</div>";
			$jsonAttendance[$employee->getMasterId()] = array('employee' => $eCell,
															  'employee-name' => $employee->getName(),
															  'teamId' => $employee->getDefaultTeam()->getMasterId(),
															  'team' => $employee->getDefaultTeam()->getName(),
															  'shift' => $employee->getDefaultShift()->getId(),
															  'total'=>0);
			for ($i=0; $i < $n; $i++) {$jsonAttendance[$employee->getMasterId()][($i+1)] = $hourStructure;}
		}
	    
	    return $jsonAttendance; 
	}

	public function getAttendanceReportStructure($year, $month, $n)
	{
		$teams = $this->em->getRepository('SEInputBundle:Team')->getHistoricalTeams($year,$month);
		$data = array(
			'report' => array(
				'attrate' => 0,
				'hc' => 0,
				'presence' => 0,
				'totalhr' => 0,
				'totalreghr' => 0,
				'totalothr' => 0,
				'wdot' => 0,
				'weot' => 0
			),
			'attrate' => array('cat' => array(), 'data' => array()),
			'otconso' => array('cat' => array(), 'data' => array()),
			'topot' => array('cat' => array(), 'data' => array()),
			'dailyot' => array('cat' => array(), 'data' => array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0))
		);

		$temp = array('pres' => 0, 'hc' => 0);

		$json = array();
		$json[0][0] = $data;
		for ($j=1; $j <= $n ; $j++) {$json[0][0]['attrate']['temp'][$j] = $temp;}

		foreach ($teams as $t) {
			$tId = $t->getMasterId();
			$json[$tId][0] = $data;
			for ($j=1; $j <= $n ; $j++) {$json[$tId][0]['attrate']['temp'][$j] = $temp;}
			for ($i=0; $i < $t->getShiftNb(); $i++) { 
				$sId = $i+1;
				$json[$tId][$sId] = $data;
				for ($j=1; $j <= $n ; $j++) {$json[$tId][$sId]['attrate']['temp'][$j] = $temp;}
			}
		}
		return $json;
	}

	public function forgetAttendanceDataKeys($data){

		foreach ($data as $t => $team) { 
			foreach ($team as $s => $shift) {
			 	if(isset( $data[$t][$s] , $data )){
					$data[$t][$s]['attrate']['data'] = array_values( $data[$t][$s]['attrate']['data'] );
					$data[$t][$s]['otconso']['data'] = array_values( $data[$t][$s]['otconso']['data'] );
					$data[$t][$s]['dailyot']['data'] = array_values( $data[$t][$s]['dailyot']['data'] );
				}
			}
		}
		return $data;
	}
}
