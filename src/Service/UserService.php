<?php


namespace App\Service;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService
{
	private EntityManagerInterface $entityManager;
	private UserPasswordEncoderInterface $encoder;
	//private TokenStorageInterface $tokenStorage;*/

	public function __construct(EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
	{
		$this->entityManager = $manager;
		$this->encoder = $encoder;
	}

	public function oneByLogin(string $login) : ?User
	{
		return $this->entityManager
			->getRepository(User::class)
			->findOneBy(['username' => $login]);
	}

	public function create(string $username, string $name, string $password, array $roles) : User
	{
		$user = new User();

		$encodedPassword = $this->encoder->encodePassword($user, $password);

		$user->setUsername($username);
		$user->setName($name);
		$user->setPassword($encodedPassword);
		$user->setRoles($roles);

		$this->entityManager->persist($user);
		$this->entityManager->flush();

		return $user;
	}

	public function persist(User $user) : void
	{
		$this->entityManager->persist($user);
		$this->entityManager->flush();
	}

}