<?php

namespace App\Repository;

use App\Entity\ChallengeDifficulty;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ChallengeDifficulty>
 *
 * @method ChallengeDifficulty|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChallengeDifficulty|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChallengeDifficulty[]    findAll()
 * @method ChallengeDifficulty[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChallengeDifficultyRepository extends ServiceEntityRepository {

	public function __construct(ManagerRegistry $registry) {
		parent::__construct($registry, ChallengeDifficulty::class);
	}

	public function save(ChallengeDifficulty $entity, bool $flush = false): void {
		$this->getEntityManager()->persist($entity);

		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}

	public function remove(ChallengeDifficulty $entity, bool $flush = false): void {
		$this->getEntityManager()->remove($entity);

		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}

}
