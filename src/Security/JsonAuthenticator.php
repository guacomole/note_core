<?php

namespace App\Security;

use App\Entity\User;
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

	private const LOGIN_ROUTE = RouteEnum::LOGIN;

	private $entityManager;

	private $urlGenerator;

	private $passwordEncoder;

	public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator, UserPasswordEncoderInterface $passwordEncoder)
	{
		$this->entityManager = $entityManager;
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
		$parameters = json_decode($request->getContent(), true);

		if (!isset($parameters['username'])) {
			throw new UnprocessableEntityHttpException('Поле "username" не должно быть пустым');
		}

		if (!isset($parameters['password'])) {
			throw new UnprocessableEntityHttpException('Поле "password" не должно быть пустым');
		}

		$credentials = [
			'username' => $parameters['username'],
			'password' => $parameters['password'],
		];

		return $credentials;
	}

	public function getUser($credentials, UserProviderInterface $userProvider)
	{
		$user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $credentials['username']]);

		if (!$user) {
			throw new UnprocessableEntityHttpException('Неправильный логин и/или пароль.');
		}

		/*if ($user->getStatus() == UserEnum::BANNED) {
			throw new UnprocessableEntityHttpException('Пользователь был заблокирован. Обратитесь к администратору');
		}*/

		return $user;
	}

	public function checkCredentials($credentials, UserInterface $user)
	{
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