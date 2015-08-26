<?php

namespace SE\InputBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * SAPRFRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SAPRFRepository extends EntityRepository
{

	public function getTo($date, $user){

		$qb = $this
			->createQueryBuilder('a')
			->select("a")
			->where("a.dateImport = :date")
            ->andWhere("a.recorded is NULL OR a.recorded = 0")
            ->andWhere("a.user = :user");

  		$qb->setParameter('date', $date->format('Y-m-d H:i:s'));
  		$qb->setParameter('user', $user);

  		return $qb->getQuery()->getResult();
	}

	public function getRecordedTo($date, $user){

		$qb = $this
			->createQueryBuilder('a')
			->select("a")
			->where("a.dateImport = :date")
            ->andWhere("a.recorded = 1")
            ->andWhere("a.user = :user");

  		$qb->setParameter('date', $date->format('Y-m-d H:i:s'));
  		$qb->setParameter('user', $user);

  		return $qb->getQuery()->getResult();
	}
}