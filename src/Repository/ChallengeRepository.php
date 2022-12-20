<?php

namespace App\Repository;

use App\Entity\Challenge;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Challenge>
 *
 * @method Challenge|null find($id, $lockMode = null, $lockVersion = null)
 * @method Challenge|null findOneBy(array $criteria, array $orderBy = null)
 * @method Challenge[]    findAll()
 * @method Challenge[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChallengeRepository extends ServiceEntityRepository {

	public function __construct(ManagerRegistry $registry) {
		parent::__construct($registry, Challenge::class);
	}

	public function save(Challenge $entity, bool $flush = false): void {
		$this->getEntityManager()->persist($entity);

		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}

	public function remove(Challenge $entity, bool $flush = false): void {
		$this->getEntityManager()->remove($entity);

		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}


	public function findOrderedByGameName(int $limit = 10, int $offset = 0): array {
		return $this->createQueryBuilder('c')
				->select('c')
				->innerJoin('c.game', 'g')
				->orderBy('g.name')
				->setMaxResults($limit)
				->setFirstResult($offset)
				->getQuery()
				->getResult();
	}


	public function findOrderedByPeriod(int $limit = 10, int $offset = 0): array {
		return $this->createQueryBuilder('c')
				->select('c')
				->innerJoin('c.periods', 'p')
				->orderBy('p.year', 'ASC')
				->orderBy('p.id', 'ASC')
				->setMaxResults($limit)
				->setFirstResult($offset)
				->getQuery()
				->getResult();
	}

}
