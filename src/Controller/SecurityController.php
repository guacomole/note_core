<?php

namespace App\Controller;

use App\Enum\RoleEnum;
use App\Handler\SessionHandler;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\User;
use FOS\RestBundle\Controller\Annotations as Rest;

class SecurityController extends AbstractFOSRestController
{
	/**
	 * @Route("/login", name="login", methods={"POST"})
	 */
	public function login(Request $request, SessionHandler $sessionHandler): Response
	{
		/**
		 * @var User $user
		 */
		$user = $this->getUser();
		$sessionStorage = new NativeSessionStorage(['use_cookies' => false], $sessionHandler);
		$session = new Session($sessionStorage);

		$session->set('username', $user->getUsername());
		$session->save();

		$request->setSession($session);

		return $this->json([
			'id' => $user->getId(),
			'username' => $user->getUsername(),
			'roles' => $user->getRoles(),
			'token' => $session->getId()
		]);

	}

	/**
	 * @Rest\Post("/random-password")
	 *
	 */
	public function generatePassword()
	{
		return null;
	}

}