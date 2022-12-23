<?php

namespace App\Repository;

use App\Entity\Period;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Period>
 *
 * @method Period|null find($id, $lockMode = null, $lockVersion = null)
 * @method Period|null findOneBy(array $criteria, array $orderBy = null)
 * @method Period[]    findAll()
 * @method Period[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PeriodRepository extends ServiceEntityRepository {

	public function __construct(ManagerRegistry $registry) {
		parent::__construct($registry, Period::class);
	}

	public function save(Period $entity, bool $flush = false): void {
		$this->getEntityManager()->persist($entity);

		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}

	public function remove(Period $entity, bool $flush = false): void {
		$this->getEntityManager()->remove($entity);

		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}


	/**
	 * @return Period[]
	 */
	public function findCurrentPeriods(): array {
		$now = new DateTime('now');

		return $this->createQueryBuilder('p')
			->select('p')
			->where('p.start_date < :now')
			->andWhere('p.end_date > :now')
			->setParameter('now', $now)
			->getQuery()
			->getResult();
	}

}
