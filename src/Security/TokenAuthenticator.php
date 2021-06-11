<?php


namespace App\Security;

use App\API\v1\Service\CoreService;
use App\Entity\User;
use App\Enum\RouteEnum;
use App\Enum\UserNameEnum;
use App\Service\DocumentService;
use App\Service\SessionService;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class TokenAuthenticator extends AbstractGuardAuthenticator
{
	private $em;
	private ?UserService $userService;
	private ?SessionService $sessionService;
	private ?CoreService $coreApiService;
	private ?DocumentService $documentService;
	private ?Request $request;

	private const LOGIN_ROUTE = RouteEnum::LOGIN_API_V1;

	private const TOKEN_HEADER = 'token';

	public function __construct(EntityManagerInterface $em, UserService $userService, SessionService $sessionService, CoreService $coreApiService, DocumentService $documentService, RequestStack $requestStack)
	{
		$this->em = $em;
		$this->userService = $userService;
		$this->sessionService = $sessionService;
		$this->coreApiService = $coreApiService;
		$this->documentService = $documentService;
		$this->request = $requestStack->getCurrentRequest();
	}

	/**
	 * Called on every request to decide if this authenticator should be
	 * used for the request. Returning `false` will cause this authenticator
	 * to be skipped.
	 */
	public function supports(Request $request)
	{
		return $request->get('_route') !== self::LOGIN_ROUTE;
	}

	/**
	 * Called on every request. Return whatever credentials you want to
	 * be passed to getUser() as $credentials.
	 */
	public function getCredentials(Request $request)
	{
		return $request->headers->get(self::TOKEN_HEADER) ? $request->headers->get(self::TOKEN_HEADER) : '';
	}

	public function getUser($credentials, UserProviderInterface $userProvider)
	{
		$session = $this->sessionService->oneByIdAndNotExpired($credentials);

		if (!$session || !$session->getUser()->first()) {
			try {
				return $this->coreApiService->getUserByToken($credentials);
			} catch (UnauthorizedHttpException $e) {
				if ($this->request->query->get('token') && $this->documentService->oneByTokenAndId($this->request->query->get('token'), $this->request->attributes->get('id'))) {
					return $this->userService->oneByLogin(UserNameEnum::PARTNER);
				}

				return null;
			}
		}

		return $session->getUser()->first();
	}

	public function checkCredentials($credentials, UserInterface $user)
	{
		// Check credentials - e.g. make sure the password is valid.
		// In case of an API token, no credential check is needed.

		// Return `true` to cause authentication success
		return true;
	}

	public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
	{
		// on success, let the request continue
		return null;
	}

	public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
	{
		$data = [
			// you may want to customize or obfuscate the message first
			'message' => strtr($exception->getMessageKey(), $exception->getMessageData())

			// or to translate this message
			// $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
		];

		return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
	}

	/**
	 * Called when authentication is needed, but it's not sent
	 */
	public function start(Request $request, AuthenticationException $authException = null)
	{
		$data = [
			// you might translate this message
			'message' => '"token" header required for authentication'
		];

		return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
	}

	public function supportsRememberMe()
	{
		return false;
	}
}