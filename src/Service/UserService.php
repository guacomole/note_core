<?php


namespace App\Service;


use App\Entity\User;
use App\Enum\RoleEnum;
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

	public function create(string $username, string $name, string $password, string $email, string $phone, string $role) : User
	{
		$user = new User();

		$encodedPassword = $this->encoder->encodePassword($user, $password);

		$user->setUsername($username);
		$user->setName($name);
		$user->setPassword($encodedPassword);
		$user->setEmail($email);
		$user->setPhone($phone);

		$user->setRoles([$role]);

		$this->entityManager->persist($user);
		$this->entityManager->flush();

		return $user;
	}

	public function update(string $username, string $name = null, string $email = null, string $phone = null, array $roles = null) : User
	{
		$user = $this->oneByLogin($username);

		$name ? $user->setName($name) : null;
		$phone ? $user->setPhone($phone) : null;
		$roles ? $user->setRoles($roles) : null;

		$user->setEmail($email);
		$user->setPhone($phone);
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

	public function getAvailableRoles(User $user)
	{

		if ($user->hasRole(RoleEnum::ROLE_ADMIN)) {
			return [
				['name' => 'Пользователь', 'role' => RoleEnum::ROLE_USER],
				['name' => 'Врач', 'role' => RoleEnum::ROLE_MASTER],
				['name' => 'Администратор', 'role' => RoleEnum::ROLE_ADMIN]];
		}

		return [['name' => 'Пользователь', 'role' => RoleEnum::ROLE_USER]];
	}

	public function getAvailableRolesRaw(User $user) : array
	{
		return array_column($this->getAvailableRoles($user), 'role');
	}

	public function isAvailableRolesByUser(array $roles, User $user) : bool
	{
		$availableRoles = $this->getAvailableRolesRaw($user);

		foreach ($roles as $role) {
			if (!in_array($role, $availableRoles)) {
				return false;
			}
		}

		return true;
	}

}