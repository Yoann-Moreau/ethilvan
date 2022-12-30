<?php

namespace App\Repository;

use App\Entity\Cup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cup>
 *
 * @method Cup|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cup|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cup[]    findAll()
 * @method Cup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CupRepository extends ServiceEntityRepository {

	public function __construct(ManagerRegistry $registry) {
		parent::__construct($registry, Cup::class);
	}

	public function save(Cup $entity, bool $flush = false): void {
		$this->getEntityManager()->persist($entity);

		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}

	public function remove(Cup $entity, bool $flush = false): void {
		$this->getEntityManager()->remove($entity);

		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}

}
