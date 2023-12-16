<?php


namespace App\Repository;


use App\Entity\Game;
use App\Entity\Period;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @extends ServiceEntityRepository<Game>
 *
 * @method Game|null find($id, $lockMode = null, $lockVersion = null)
 * @method Game|null findOneBy(array $criteria, array $orderBy = null)
 * @method Game[]    findAll()
 * @method Game[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameRepository extends ServiceEntityRepository {

	public function __construct(ManagerRegistry $registry) {
		parent::__construct($registry, Game::class);
	}

	public function save(Game $entity, bool $flush = false): void {
		$this->getEntityManager()->persist($entity);

		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}

	public function remove(Game $entity, bool $flush = false): void {
		$this->getEntityManager()->remove($entity);

		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}


	public function createAlphabeticalQueryBuilder(): QueryBuilder {
		return $this->createQueryBuilder('g')
				->select('g')
				->orderBy('g.name', 'ASC');
	}


	/**
	 * @param Period[]|null $periods
	 * @return array
	 */
	public function getGamesWithChallenges(array $periods = null): array {
		$query_builder = $this->createQueryBuilder('g')
				->innerJoin('g.challenges', 'c');

		if ($periods !== null) {
			$query_builder->innerJoin('c.periods', 'p')
				->where('p IN (:periods)')
				->setParameter('periods', $periods);
		}

		return $query_builder
				->orderBy('g.name')
				->getQuery()
				->getResult();
	}


	public function getGamesWithValidSubmissionsForUser(User $user): array {
		return $this->createQueryBuilder('g')
				->innerJoin('g.challenges', 'c')
				->innerJoin('c.submissions', 's')
				->where('s.valid = true')
				->andWhere('s.user = :user')
				->setParameter('user', $user)
				->orderBy('g.name', 'ASC')
				->getQuery()
				->getResult();
	}

}
