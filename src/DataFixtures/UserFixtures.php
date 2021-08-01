<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Enum\RoleEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{

	private $passwordHasher;

	public function __construct(UserPasswordEncoderInterface $passwordHasher)
	{
		$this->passwordHasher = $passwordHasher;
	}

	public function load(ObjectManager $manager)
	{
		$user = new User();

		$user->setUsername('user');
		$user->setPassword($this->passwordHasher->encodePassword($user, 'Wwwqqq111'));
		$user->setRoles([RoleEnum::ROLE_USER]);

		$manager->persist($user);

		$user = new User();

		$user->setUsername('master');
		$user->setPassword($this->passwordHasher->encodePassword($user, 'Wwwqqq111'));
		$user->setRoles([RoleEnum::ROLE_MASTER]);

		$manager->persist($user);

		$user = new User();

		$user->setUsername('admin');
		$user->setPassword($this->passwordHasher->encodePassword($user, 'Wwwqqq111'));
		$user->setRoles([RoleEnum::ROLE_ADMIN]);

		$manager->persist($user);

		$manager->flush();

	}
}
