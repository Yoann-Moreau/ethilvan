<?php

namespace App\Repository;

use App\Entity\SubmissionMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SubmissionMessage>
 *
 * @method SubmissionMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method SubmissionMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method SubmissionMessage[]    findAll()
 * @method SubmissionMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubmissionMessageRepository extends ServiceEntityRepository {

	public function __construct(ManagerRegistry $registry) {
		parent::__construct($registry, SubmissionMessage::class);
	}

	public function save(SubmissionMessage $entity, bool $flush = false): void {
		$this->getEntityManager()->persist($entity);

		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}


	public function remove(SubmissionMessage $entity, bool $flush = false): void {
		$this->getEntityManager()->remove($entity);

		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}

}
