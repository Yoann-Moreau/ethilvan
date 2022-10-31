<?php

namespace App\Repository;

use App\Entity\SteamGame;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SteamGame>
 *
 * @method SteamGame|null find($id, $lockMode = null, $lockVersion = null)
 * @method SteamGame|null findOneBy(array $criteria, array $orderBy = null)
 * @method SteamGame[]    findAll()
 * @method SteamGame[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SteamGameRepository extends ServiceEntityRepository {

	public function __construct(ManagerRegistry $registry) {
		parent::__construct($registry, SteamGame::class);
	}

	public function save(SteamGame $entity, bool $flush = false): void {
		$this->getEntityManager()->persist($entity);

		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}

	public function remove(SteamGame $entity, bool $flush = false): void {
		$this->getEntityManager()->remove($entity);

		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}

}
