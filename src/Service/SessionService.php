<?php


namespace App\Service;

use App\Entity\Sessions;
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
		return $this->entityManager->getRepository(Sessions::class)->findOneBy(['id' => $id]);
	}

	public function oneByIdAndNotExpired(string $id) : ?Sessions
	{
		return $this->entityManager->getRepository(Sessions::class)->oneByIdAndNotExpired($id);
	}

	public function update(Sessions $session) : void
	{
		$this->entityManager->persist($session);
		$this->entityManager->flush();
	}

	public function removeSessionById(string $id): void
	{
		$session = $this->oneById($id);

		$this->entityManager->remove($session);
		$this->entityManager->flush();
	}

	public function removeUnnecessarySessionByUser(User $user, Sessions $session)
	{
		return $this->entityManager->getRepository(Session::class)->removeUnnecessarySessionByUser($user, $session);
	}

}
