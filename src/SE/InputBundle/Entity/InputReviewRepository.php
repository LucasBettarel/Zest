<?php

namespace SE\InputBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * InputReviewRepository
 *
 */
class InputReviewRepository extends EntityRepository
{
	public function getLastMonthMissingInputErrors()
	{
		$now = new \DateTime();
		$lastMonth = new \DateTime();
		$lastMonth->modify( '-'.(date('j')).' day' );

		$qb = $this
			->createQueryBuilder('a')
			->select("a")
			->leftJoin('a.type', 't')
			->addSelect('t')
			->where("a.date <= '".$now->format("Y-m-d H:i:s")."'")
            ->andWhere("a.date > '".$lastMonth->format("Y-m-d H:i:s")."'")
            ->andWhere('t.id = 2')
            ->andWhere('a.status = 0')
		    ->orderBy('a.date', 'DESC')
			->getQuery()
			->getResult()
  		;
  
		return $qb;
	}

	public function getLastMonthImportErrors()
	{
		$now = new \DateTime();
		$lastMonth = new \DateTime();
		$lastMonth->modify( '-'.(date('j')).' day' );

		$qb = $this
			->createQueryBuilder('a')
			->select("a")
			->leftJoin('a.type', 't')
			->addSelect('t')
			->where("a.date <= '".$now->format("Y-m-d H:i:s")."'")
            ->andWhere("a.date > '".$lastMonth->format("Y-m-d H:i:s")."'")
            ->andWhere('t.id = 1')
            ->andWhere('a.status = 0')
		    ->orderBy('a.date', 'DESC')
			->getQuery()
			->getResult()
  		;
  
		return $qb;
	}

	public function getLastMonthToErrors()
	{
		$now = new \DateTime();
		$lastMonth = new \DateTime();
		$lastMonth->modify( '-'.(date('j')).' day' );

		$qb = $this
			->createQueryBuilder('a')
			->select("a")
			->leftJoin('a.type', 't')
			->addSelect('t')
			->where("a.date <= '".$now->format("Y-m-d H:i:s")."'")
            ->andWhere("a.date > '".$lastMonth->format("Y-m-d H:i:s")."'")
            ->andWhere('t.id = 3')
            ->andWhere('a.status = 0')
		    ->orderBy('a.date', 'DESC')
			->getQuery()
			->getResult()
  		;
  
		return $qb;
	}

	public function getLastMonthHoursErrors()
	{
		$now = new \DateTime();
		$lastMonth = new \DateTime();
		$lastMonth->modify( '-'.(date('j')).' day' );

		$qb = $this
			->createQueryBuilder('a')
			->select("a")
			->leftJoin('a.type', 't')
			->addSelect('t')
			->where("a.date <= '".$now->format("Y-m-d H:i:s")."'")
            ->andWhere("a.date > '".$lastMonth->format("Y-m-d H:i:s")."'")
            ->andWhere('t.id = 4')
            ->andWhere('a.status = 0')
		    ->orderBy('a.date', 'DESC')
			->getQuery()
			->getResult()
  		;
  
		return $qb;
	}

		public function getMonthMissingInputErrors($month, $year)
	{
		$start = new \DateTime();
		$end = new \DateTime();
		$start->setDate($year, $month, 1);
		$end = $start->format( 'Y-m-t H:i:s' );
		$start->modify( '-1 day' );

		$qb = $this
			->createQueryBuilder('a')
			->select("a")
			->leftJoin('a.type', 't')
			->addSelect('t')
			->where("a.date <= '".$end."'")
            ->andWhere("a.date > '".$start->format("Y-m-d H:i:s")."'")
		    ->andWhere('t.id = 2')
            ->andWhere('a.status = 0')
		    ->orderBy('a.date', 'DESC')
			->getQuery()
			->getResult()
  		;
  
		return $qb;
	}

	public function getMonthImportErrors($month, $year)
	{
		$start = new \DateTime();
		$end = new \DateTime();
		$start->setDate($year, $month, 1);
		$end = $start->format( 'Y-m-t H:i:s' );
		$start->modify( '-1 day' );

		$qb = $this
			->createQueryBuilder('a')
			->select("a")
			->leftJoin('a.type', 't')
			->addSelect('t')
			->where("a.date <= '".$end."'")
            ->andWhere("a.date > '".$start->format("Y-m-d H:i:s")."'")
		    ->andWhere('t.id = 1')
            ->andWhere('a.status = 0')
		    ->orderBy('a.date', 'DESC')
			->getQuery()
			->getResult()
  		;
  
		return $qb;
	}

	public function getMonthToErrors($month, $year)
	{
		$start = new \DateTime();
		$end = new \DateTime();
		$start->setDate($year, $month, 1);
		$end = $start->format( 'Y-m-t H:i:s' );
		$start->modify( '-1 day' );

		$qb = $this
			->createQueryBuilder('a')
			->select("a")
			->leftJoin('a.type', 't')
			->addSelect('t')
			->where("a.date <= '".$end."'")
            ->andWhere("a.date > '".$start->format("Y-m-d H:i:s")."'")
		    ->andWhere('t.id = 3')
            ->andWhere('a.status = 0')
		    ->orderBy('a.date', 'DESC')
			->getQuery()
			->getResult()
  		;
  
		return $qb;
	}

	public function getMonthHoursErrors($month, $year)
	{
		$start = new \DateTime();
		$end = new \DateTime();
		$start->setDate($year, $month, 1);
		$end = $start->format( 'Y-m-t H:i:s' );
		$start->modify( '-1 day' );

		$qb = $this
			->createQueryBuilder('a')
			->select("a")
			->leftJoin('a.type', 't')
			->addSelect('t')
			->where("a.date <= '".$end."'")
            ->andWhere("a.date > '".$start->format("Y-m-d H:i:s")."'")
		    ->andWhere('t.id = 4')
            ->andWhere('a.status = 0')
		    ->orderBy('a.date', 'DESC')
			->getQuery()
			->getResult()
  		;
  
		return $qb;
	}
}
