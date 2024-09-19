<?php

namespace App\Repository;

use App\Entity\Challenge;
use App\Entity\ChallengeDifficulty;
use App\Entity\Period;
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


	public function search(string $search = '', int $limit = 10, int $offset = 0, string $sort_by = ''): array {
		$query_builder = $this->createQueryBuilder('c')
				->select('c')
				->innerJoin('c.game', 'g')
				->innerJoin('c.difficulty', 'd')
				->innerJoin('c.periods', 'p');

		if (!empty($search)) {
			$query_builder
					->where('c.name LIKE :search')
					->orWhere('g.name LIKE :search')
					->orWhere('p.name LIKE :search')
					->orWhere('d.name LIKE :search')
					->setParameter('search', '%' . $search . '%');
		}

		if ($sort_by === 'game') {
			$query_builder->orderBy('g.name', 'ASC');
		}
		elseif ($sort_by === 'difficulty') {
			$query_builder->orderBy('c.difficulty', 'ASC');
		}
		elseif ($sort_by === 'period') {
			$query_builder->orderBy('p.year', 'ASC');
			$query_builder->orderBy('p.id', 'ASC');
		}
		else {
			$query_builder->orderBy('c.id', 'DESC');
		}

		if ($limit !== 0) {
			$query_builder
					->setMaxResults($limit)
					->setFirstResult($offset);
		}

		return $query_builder
				->getQuery()
				->getResult();
	}


	public function countWithSearch(string $search = ''): int {
		$query_builder = $this->createQueryBuilder('c')
				->select('count(c.id)')
				->innerJoin('c.game', 'g')
				->innerJoin('c.difficulty', 'd')
				->innerJoin('c.periods', 'p');

		if (!empty($search)) {
			$query_builder
					->where('c.name LIKE :search')
					->orWhere('g.name LIKE :search')
					->orWhere('p.name LIKE :search')
					->orWhere('d.name LIKE :search')
					->setParameter('search', '%' . $search . '%');
		}

		return $query_builder->getQuery()->getSingleScalarResult();
	}


	public function countByPeriodAndDifficulty(Period $period, ChallengeDifficulty $difficulty = null): int {
		$query_builder = $this->createQueryBuilder('c')
				->select('count(c.id)')
				->innerJoin('c.difficulty', 'd')
				->innerJoin('c.periods', 'p')
				->where('p = :p')
				->setParameter('p', $period);

		if ($difficulty !== null) {
			$query_builder->andWhere('d = :d')
					->setParameter('d', $difficulty);
		}

		return $query_builder->getQuery()->getSingleScalarResult();
	}

}
