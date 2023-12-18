<?php

namespace App\Repository;

use App\Entity\Submission;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Submission>
 *
 * @method Submission|null find($id, $lockMode = null, $lockVersion = null)
 * @method Submission|null findOneBy(array $criteria, array $orderBy = null)
 * @method Submission[]    findAll()
 * @method Submission[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubmissionRepository extends ServiceEntityRepository {

	public function __construct(ManagerRegistry $registry) {
		parent::__construct($registry, Submission::class);
	}

	public function save(Submission $entity, bool $flush = false): void {
		$this->getEntityManager()->persist($entity);

		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}


	public function remove(Submission $entity, bool $flush = false): void {
		$this->getEntityManager()->remove($entity);

		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}


	public function search(?bool $valid = null, string $search = '', int $limit = 10, int $offset = 0): array {
		$query_builder = $this->createQueryBuilder('s')
				->innerJoin('s.challenge', 'c')
				->innerJoin('s.user', 'u')
				->innerJoin('c.game', 'g')
				->innerJoin('c.difficulty', 'd')
				->innerJoin('s.period', 'p');

		if ($valid !== null) {
			$query_builder->andWhere('s.valid = :valid')
				->setParameter('valid', $valid);
		}

		if (!empty($search)) {
			$query_builder
					->andWhere('c.name LIKE :search OR g.name LIKE :search OR p.name LIKE :search OR d.name LIKE :search OR u.username LIKE :search')
					->setParameter('search', '%' . $search . '%');
		}

		if ($valid) {
			$query_builder->orderBy('s.validation_date', 'DESC');
		}
		else {
			$query_builder->orderBy('s.submission_date', 'ASC');
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


	public function countWithSearch(?bool $valid = null, string $search = ''): int {
		$query_builder = $this->createQueryBuilder('s')
				->select('count(s.id)')
				->innerJoin('s.challenge', 'c')
				->innerJoin('s.user', 'u')
				->innerJoin('c.game', 'g')
				->innerJoin('c.difficulty', 'd')
				->innerJoin('s.period', 'p');

		if ($valid !== null) {
			$query_builder->andWhere('s.valid = :valid')
					->setParameter('valid', $valid);
		}

		if (!empty($search)) {
			$query_builder
					->andWhere('c.name LIKE :search OR g.name LIKE :search OR p.name LIKE :search OR d.name LIKE :search OR u.username LIKE :search')
					->setParameter('search', '%' . $search . '%');
		}

		return $query_builder
				->getQuery()
				->getSingleScalarResult();
	}


	public function searchValidForUser(User $user, string $search = '', int $limit = 10, int $offset = 0,
			string $sort_by = ''): array {

		$query_builder = $this->createQueryBuilder('s')
				->innerJoin('s.challenge', 'c')
				->innerJoin('c.game', 'g')
				->innerJoin('c.difficulty', 'd')
				->innerJoin('s.period', 'p')
				->where('s.user = :user')
				->setParameter('user', $user)
				->andWhere('s.valid = true');

		if (!empty($search)) {
			$query_builder
					->andWhere('c.name LIKE :search OR g.name LIKE :search OR p.name LIKE :search OR d.name LIKE :search')
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
			$query_builder->orderBy('p.start_date', 'ASC');
		}
		else {
			$query_builder->orderBy('s.validation_date', 'DESC');
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


	public function countValidForUserWithSearch(User $user, string $search = ''): int {
		$query_builder = $this->createQueryBuilder('s')
				->select('count(s.id)')
				->innerJoin('s.challenge', 'c')
				->innerJoin('c.game', 'g')
				->innerJoin('c.difficulty', 'd')
				->innerJoin('s.period', 'p')
				->where('s.user = :user')
				->setParameter('user', $user)
				->andWhere('s.valid = true');

		if (!empty($search)) {
			$query_builder
					->andWhere('c.name LIKE :search OR g.name LIKE :search OR p.name LIKE :search OR d.name LIKE :search')
					->setParameter('search', '%' . $search . '%');
		}

		return $query_builder->getQuery()->getSingleScalarResult();
	}


	public function findValidForUser(User $user): array {
		return $this->createQueryBuilder('s')
				->innerJoin('s.period', 'p')
				->innerJoin('s.challenge', 'c')
				->innerJoin('c.game', 'g')
				->innerJoin('c.difficulty', 'd')
				->where('s.valid = true')
				->andWhere('s.user = :user')
				->setParameter('user', $user)
				->orderBy('g.name', 'ASC')
				->orderBy('p.start_date', 'ASC')
				->orderBy('d.id', 'ASC')
				->getQuery()
				->getResult();
	}

}
