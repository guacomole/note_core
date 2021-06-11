<?php


namespace App\Service;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService
{
	private ?EntityManagerInterface $entityManager = null;

	private ?UserPasswordEncoderInterface $encoder = null;

	private ?TokenStorageInterface $tokenStorage;

	public function __construct(EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, TokenStorageInterface $tokenStorage)
	{
		$this->entityManager = $manager;
		$this->encoder = $encoder;
		$this->tokenStorage = $tokenStorage;
	}

	public function createOrUpdate(User $user) : void
	{
		$this->entityManager->persist($user);
		$this->entityManager->flush();
	}

	public function oneByLogin(string $login) : ?User
	{
		return $this->entityManager
			->getRepository(User::class)
			->findOneBy(['username' => $login]);
	}
}