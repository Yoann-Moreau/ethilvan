<?php

namespace App\Repository;

use App\Entity\AdminNotification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AdminNotification>
 *
 * @method AdminNotification|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdminNotification|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdminNotification[]    findAll()
 * @method AdminNotification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdminNotificationRepository extends ServiceEntityRepository {

	public function __construct(ManagerRegistry $registry) {
		parent::__construct($registry, AdminNotification::class);
	}

	public function save(AdminNotification $entity, bool $flush = false): void {
		$this->getEntityManager()->persist($entity);

		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}


	public function remove(AdminNotification $entity, bool $flush = false): void {
		$this->getEntityManager()->remove($entity);

		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}

}
