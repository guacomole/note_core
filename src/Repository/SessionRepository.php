<?php

namespace App\Repository;

use App\Entity\Session;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Session|null find($id, $lockMode = null, $lockVersion = null)
 * @method Session|null findOneBy(array $criteria, array $orderBy = null)
 * @method Session[]    findAll()
 * @method Session[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Session::class);
    }

	/**
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function oneByIdAndNotExpired(string $id) : ?Session
	{
		$query = $this->createQueryBuilder('s')
			->where('s.id = :id')
			->andWhere('s.sess_lifetime > :sessionLifetime')
			->setParameter('id', $id)
			->setParameter('sessionLifetime', (new \DateTime())->getTimestamp());

		return $query->getQuery()->getOneOrNullResult();
	}

	public function removeUnnecessarySessionByUser(User $user, Session $session)
	{
		$query = $this->createQueryBuilder('s')
			->from(Session::class, 'session')
			->innerJoin('session.user', 'user')
			->where('user = ?1')
			->andWhere('session != ?2')
			->setParameter(1, $user->getId())
			->setParameter(2, $session->getId())
			->select('session.id')
			->distinct();

		$qb = $this->createQueryBuilder('s1')
			->delete(Session::class, 's1')
			->where('s1.id IN ( ?3 )')
			->setParameter('3', $query->getQuery()->execute());

		return $qb->getQuery()->execute();
	}
}
