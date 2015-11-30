<?php

namespace SE\InputBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * EmployeeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EmployeeRepository extends EntityRepository
{
	public function getAlphaCurrentEmployees()
	{
		$qb = $this
		->createQueryBuilder('a')
		->select("a")
		->leftJoin('a.status', 's')
		->addSelect('s')
		->where("s.id = 1")
		->andWhere("a.statusControl = 1")
        ->orderBy('a.sesa', 'ASC')
		->getQuery()
		->getResult()
		;
		return $qb;
	}

	public function getMaxId()
	{
	    $qb = $this
	    ->createQueryBuilder('s')
	    ->select('MAX(s.masterId)')
    	->setMaxResults(1)
		->getQuery()
		->getResult()
		;
		return $qb;
	}

	public function getCurrentEmployees()
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

	public function getHistoricalEmployees($year, $month)
	{
		$start = new \DateTime();
		$end = new \DateTime();
		$start->setDate($year, $month, 1);
		$end = $start->format( 'Y-m-t' );

		$qb = $this
		->createQueryBuilder('a')
		->select("a")
		->where("( a.endDate IS NOT NULL and a.endDate >= '".$end."' ) or ( a.endDate IS NULL and a.statusControl = 1 ) ")
        ->andWhere("a.startDate <= '".$end."'")
	    ->orderBy('a.masterId', 'ASC')
		->getQuery()
		->getResult()
		;
		return $qb;
	}
}
