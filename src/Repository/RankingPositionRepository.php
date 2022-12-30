<?php

namespace App\Repository;

use App\Entity\RankingPosition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RankingPosition>
 *
 * @method RankingPosition|null find($id, $lockMode = null, $lockVersion = null)
 * @method RankingPosition|null findOneBy(array $criteria, array $orderBy = null)
 * @method RankingPosition[]    findAll()
 * @method RankingPosition[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RankingPositionRepository extends ServiceEntityRepository {

	public function __construct(ManagerRegistry $registry) {
		parent::__construct($registry, RankingPosition::class);
	}

	public function save(RankingPosition $entity, bool $flush = false): void {
		$this->getEntityManager()->persist($entity);

		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}

	public function remove(RankingPosition $entity, bool $flush = false): void {
		$this->getEntityManager()->remove($entity);

		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}

}
