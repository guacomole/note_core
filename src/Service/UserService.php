<?php


namespace App\Service;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService
{
	private EntityManagerInterface $entityManager;
	/*private UserPasswordEncoderInterface $encoder;
	private TokenStorageInterface $tokenStorage;*/

	public function __construct(EntityManagerInterface $manager)
	{
		$this->entityManager = $manager;
	}

	public function oneByLogin(string $login) : ?User
	{
		return $this->entityManager
			->getRepository(User::class)
			->findOneBy(['email' => $login]);
	}

	public function persist(User $user) : void
	{
		$this->entityManager->persist($user);
		$this->entityManager->flush();
	}

}