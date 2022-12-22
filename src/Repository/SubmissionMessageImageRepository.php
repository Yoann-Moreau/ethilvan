<?php

namespace App\Repository;

use App\Entity\SubmissionMessageImage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SubmissionMessageImage>
 *
 * @method SubmissionMessageImage|null find($id, $lockMode = null, $lockVersion = null)
 * @method SubmissionMessageImage|null findOneBy(array $criteria, array $orderBy = null)
 * @method SubmissionMessageImage[]    findAll()
 * @method SubmissionMessageImage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubmissionMessageImageRepository extends ServiceEntityRepository {

	public function __construct(ManagerRegistry $registry) {
		parent::__construct($registry, SubmissionMessageImage::class);
	}

	public function save(SubmissionMessageImage $entity, bool $flush = false): void {
		$this->getEntityManager()->persist($entity);

		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}


	public function remove(SubmissionMessageImage $entity, bool $flush = false): void {
		$this->getEntityManager()->remove($entity);

		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}

}
