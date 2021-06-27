<?php

namespace App\Repository;

use App\Entity\Sessions;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sessions|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sessions|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sessions[]    findAll()
 * @method Sessions[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SessionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sessions::class);
    }

	public function oneByIdAndNotExpired(string $id) : ?Sessions
	{
		$query = $this->createQueryBuilder('s')
			->where('s.sess_id = :id')
			->andWhere('s.sess_lifetime > :sessionLifetime')
			->setParameter('id', $id)
			->setParameter('sessionLifetime', (new \DateTime())->getTimestamp());

		return $query->getQuery()->getOneOrNullResult();
	}

	public function removeUnnecessarySessionByUser(User $user, Sessions $session)
	{
		$query = $this->createQueryBuilder('s')
			->from(Sessions::class, 'session')
			->innerJoin('session.users', 'user')
			->where('user = ?1')
			->andWhere('session != ?2')
			->setParameter(1, $user->getId())
			->setParameter(2, $session->getId())
			->select('session.sess_id')
			->distinct();

		$qb = $this->createQueryBuilder('s1')
			->delete(Sessions::class, 's1')
			->where('s1.sess_id IN ( ?3 )')
			->setParameter('3', $query->getQuery()->execute());

		return $qb->getQuery()->execute();
	}

}
