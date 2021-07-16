<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Service\NormalizerService;
use App\Service\UserService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Symfony\Component\Validator\Constraints;


/**
 * @Route("/user")
 */
class UserController extends AbstractFOSRestController
{
	/**
	 * @Rest\Get("")
	 */
	public function index(UserRepository $userRepository, NormalizerService $normalizerService): Response
    {
	    $data = $normalizerService->serializeCollection(
		    $userRepository->findAll(),
		    ['id', 'username', 'roles']
	    );


	    return $this->json($data);
    }

	/**
	 * @Rest\Post("")
	 * @RequestParam(name="username", requirements={@Constraints\Length(max=20), @Constraints\NotBlank(message="Поле username не может быть пустым")}, description="Username")
	 * @RequestParam(name="password", requirements={@Constraints\NotBlank(message="Поле пароль не может быть пустым")}, description="Password")
	 * @RequestParam(name="name", requirements={@Constraints\Length(max=96)}, description="Name")
	 * @RequestParam(name="roles", requirements={@Constraints\NotBlank(message="Регистрация без указания роли не является возможной. Пожалуйста, укажите роль")}, description="Roles")
	 */
    public function create(ParamFetcher $paramFetcher, NormalizerService $normalizerService, UserService $userService): Response
    {
    	$user = $this->getUser();

    	if (!$this->isGranted('ROLE_MASTER') && !$this->isGranted('ROLE_ADMIN')) {
			throw $this->createAccessDeniedException();
	    }

    	if ($userService->oneByLogin($paramFetcher->get('username'))){
    		throw new UnprocessableEntityHttpException("Пользователь с таким логином уже существует");
	    }

        $user = $userService->create($paramFetcher->get('username'), $paramFetcher->get('name'), $paramFetcher->get('password'), $paramFetcher->get('roles'));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

	    $data = $normalizerService->serializeCollection(
		    [$user],
		    ['id', 'username', 'name', 'roles']
	    );

        return $this->json($data);
    }

}
