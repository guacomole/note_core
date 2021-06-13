<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\User;

class SecurityController extends AbstractController
{
	/**
	 * @Route("/login", name="login", methods={"POST"})
	 */
	public function login(Request $request): Response
	{
		/**
		 * @var User $user
		 */
		$user = $this->getUser();

		return $this->json([
			'username' => $user->getUsername(),
			'roles' => $user->getRoles(),
			'token' => $request->getSession()->getId()
		]);
	}
}