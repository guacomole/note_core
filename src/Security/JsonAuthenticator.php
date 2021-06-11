<?php

namespace App\API\v1\Security;

use App\API\v1\Service\CoreService;
use App\Entity\User;
use App\Enum\RoleEnum;
use App\Enum\UserEnum;
use App\Enum\RouteEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Guard\PasswordAuthenticatedInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class JsonAuthenticator extends AbstractFormLoginAuthenticator implements PasswordAuthenticatedInterface
{
	use TargetPathTrait;

	private const LOGIN_ROUTE = RouteEnum::LOGIN_API_V1;

	private $entityManager;

	private $coreService;

	private $urlGenerator;

	private $passwordEncoder;

	public function __construct(EntityManagerInterface $entityManager, CoreService $coreService, UrlGeneratorInterface $urlGenerator, UserPasswordEncoderInterface $passwordEncoder)
	{
		$this->entityManager = $entityManager;
		$this->coreService = $coreService;
		$this->urlGenerator = $urlGenerator;
		$this->passwordEncoder = $passwordEncoder;
	}

	public function supports(Request $request)
	{
		return self::LOGIN_ROUTE === $request->attributes->get('_route')
			&& $request->isMethod('POST');
	}

	public function getCredentials(Request $request)
	{
		if (!$request->request->get('username')) {
			throw new UnprocessableEntityHttpException('Поле "username" не должно быть пустым');
		}

		if (!$request->request->get('password')) {
			throw new UnprocessableEntityHttpException('Поле "password" не должно быть пустым');
		}

		$credentials = [
			'username' => $request->request->get('username'),
			'password' => $request->request->get('password'),
		];

		return $credentials;
	}

	public function getUser($credentials, UserProviderInterface $userProvider)
	{
		$user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $credentials['username']]);

		if (!$user) {
			throw new UnprocessableEntityHttpException('Неправильный логин и/или пароль.');
		}

		if ($user->getStatus() == UserEnum::BANNED) {
			throw new UnprocessableEntityHttpException('Пользователь был заблокирован. Обратитесь к администратору');
		}

		return $user;
	}

	public function checkCredentials($credentials, UserInterface $user)
	{
		if (in_array(RoleEnum::PREMERCH, $user->getRoles())) {
			return $this->coreService->authPremerch($credentials['username'], $credentials['password']);
		}

		return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
	}

	public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey) {}

	public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
	{
		throw new UnprocessableEntityHttpException('Неправильный логин и/или пароль.');
	}

	protected function getLoginUrl()
	{
		return $this->urlGenerator->generate(self::LOGIN_ROUTE);
	}

	public function getPassword($credentials): ?string
	{
		return $credentials['password'];
	}
}