<?php

namespace App\Controller;

use App\Handler\SessionHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\User;

class SecurityController extends AbstractController
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
			'groups' => $user->getMemberGroupsForView(),
			'roles' => $user->getRoles(),
			'token' => $session->getId()
		]);

	}
}