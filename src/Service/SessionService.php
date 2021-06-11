<?php


namespace App\Service;

use App\Entity\Session;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class SessionService
{
	private $entityManager;

	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->entityManager = $entityManager;
	}

	public function oneById(string $id)
	{
		return $this->entityManager->getRepository(Session::class)->findOneBy(['id' => $id]);
	}

	public function oneByIdAndNotExpired(string $id) : ?Session
	{
		return $this->entityManager->getRepository(Session::class)->oneByIdAndNotExpired($id);
	}

	public function update(Session $session) : void
	{
		$this->entityManager->persist($session);
		$this->entityManager->flush();
	}

	public function removeUnnecessarySessionByUser(User $user, Session $session)
	{
		return $this->entityManager->getRepository(Session::class)->removeUnnecessarySessionByUser($user, $session);
	}

}