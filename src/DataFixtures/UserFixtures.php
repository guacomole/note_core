<?php

namespace App\DataFixtures;

use App\Entity\User;
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

		$user->setEmail('mail@mail.ru');
		$user->setPassword($this->passwordHasher->encodePassword($user, 'Wwwqqq111'));

		$manager->persist($user);

		$manager->flush();

	}
}
