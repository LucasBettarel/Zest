<?php

namespace SE\InputBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * DepartementRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DepartementRepository extends EntityRepository
{
	public function getCurrentDepartements()
	{
		$qb = $this
		->createQueryBuilder('a')
		->select("a")
		->where("a.statusControl = 1")
	    ->orderBy('a.masterId', 'ASC')
		->getQuery()
		->getResult()
		;
		return $qb;
	}

	public function getHistoricalDepartements($year, $month)
	{
		$start = new \DateTime();
		$end = new \DateTime();
		$start->setDate($year, $month, 1);
		$end = $start->format( 'Y-m-t' );

		$qb = $this
		->createQueryBuilder('a')
		->select("a")
		->where("( a.endDate IS NOT NULL and a.endDate >= '".$end."' ) or ( a.endDate IS NULL and a.statusControl = 1 ) ")
        ->andWhere("a.startDate <= '".$start->format("Y-m-d")."'")
	    ->orderBy('a.masterId', 'ASC')
		->getQuery()
		->getResult()
		;
		return $qb;
	}
}
