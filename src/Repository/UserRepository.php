<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface {

	public function __construct(ManagerRegistry $registry) {
		parent::__construct($registry, User::class);
	}


	public function save(User $entity, bool $flush = false): void {
		$this->getEntityManager()->persist($entity);

		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}


	public function remove(User $entity, bool $flush = false): void {
		$this->getEntityManager()->remove($entity);

		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}


	/**
	 * Used to upgrade (rehash) the user's password automatically over time.
	 */
	public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void {
		if (!$user instanceof User) {
			throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
		}

		$user->setPassword($newHashedPassword);

		$this->save($user, true);
	}


	/**
	 * Find not deleted users with the role ROLE_EV or superior
	 * @return User[]
	 */
	public function findEv(): array {
		$rsm = $this->createResultSetMappingBuilder('u');

		$sql = sprintf(
				"SELECT %s 
				FROM user u 
				WHERE (JSON_SEARCH(u.roles, 'one', :role_ev) IS NOT NULL
				OR JSON_SEARCH(u.roles, 'one', :role_admin) IS NOT NULL
				OR JSON_SEARCH(u.roles, 'one', :role_sa) IS NOT NULL)
				AND u.deleted = 0
				ORDER BY u.username ASC"
				, $rsm->generateSelectClause());

		$query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
		$query->setParameter('role_ev', 'ROLE_EV');
		$query->setParameter('role_admin', 'ROLE_ADMIN');
		$query->setParameter('role_sa', 'ROLE_SUPER_ADMIN');

		return $query->getResult();
	}

}
