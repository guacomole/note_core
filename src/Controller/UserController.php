<?php

namespace App\Controller;

use App\Entity\User;
use App\Enum\RoleEnum;
use App\Form\UserType;
use App\Handler\SessionHandler;
use App\Repository\UserRepository;
use App\Service\NormalizerService;
use App\Service\UserService;
use Doctrine\Tests\ORM\Functional\Ticket\Role;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Symfony\Component\Validator\Constraints;
use App\Validator\Constraints\ComplexPassword;


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
	 * @RequestParam(name="password", requirements={@Constraints\NotBlank(message="Поле пароль не может быть пустым"), @ComplexPassword()}, description="Password")
	 * @RequestParam(name="name", requirements={@Constraints\Length(max=96)}, description="Name")
	 * @RequestParam(name="roles", requirements={@Constraints\NotBlank(message="Регистрация без указания роли не является возможной. Пожалуйста, укажите роль"), @Constraints\A}, description="Roles")
	 */
    public function create(ParamFetcher $paramFetcher, NormalizerService $normalizerService, UserService $userService): Response
    {
    	if (!$this->isGranted('ROLE_MASTER') && !$this->isGranted('ROLE_ADMIN')) {
			throw $this->createAccessDeniedException();
	    }

    	if ($userService->oneByLogin($paramFetcher->get('username'))){
    		throw new UnprocessableEntityHttpException("Пользователь с таким логином уже существует.");
	    }

	    if($userService->isAvailableRolesByUser($paramFetcher->get('roles'), $this->getUser())) {
		    throw new UnprocessableEntityHttpException("Некорректная роль.");
	    };

        $user = $userService->create($paramFetcher->get('username'), $paramFetcher->get('name'), $paramFetcher->get('password'), $paramFetcher->get('role'));

	    $data = $normalizerService->serializeCollection(
		    [$user],
		    ['id', 'username', 'name', 'roles']
	    );

        return $this->json($data);
    }

	/**
	 * @Rest\Post("/registration")
	 * @RequestParam(name="username", requirements={@Constraints\Length(max=20), @Constraints\NotBlank(message="Поле username не может быть пустым")}, description="Username")
	 * @RequestParam(name="password", requirements={@Constraints\NotBlank(message="Поле пароль не может быть пустым"), @ComplexPassword()}, description="Password")
	 * @RequestParam(name="name", requirements={@Constraints\Length(max=96)}, description="Name")
	 */
	public function registration(Request $request, SessionHandler $sessionHandler, ParamFetcher $paramFetcher, NormalizerService $normalizerService, UserService $userService): Response
	{
		/**
		 * @var \Symfony\Component\Security\Core\User\User $user
		 */
		$user = $this->getUser();

		if ($user) {
			throw $this->createAccessDeniedException('Нельзя зарегистрироваться под авторизованным пользователем');
		}

		if ($userService->oneByLogin($paramFetcher->get('username'))) {
			throw new UnprocessableEntityHttpException("Пользователь с таким логином уже существует.");
		}

		$user = $userService->create($paramFetcher->get('username'), $paramFetcher->get('name'), $paramFetcher->get('password'), RoleEnum::ROLE_USER);

		$sessionStorage = new NativeSessionStorage(['use_cookies' => false], $sessionHandler);
		$session = new Session($sessionStorage);

		$session->set('username', $user->getUsername());
		$session->save();

		$request->setSession($session);

		$data = $normalizerService->serializeCollection(
			[$user],
			['id', 'username', 'email', 'phone', 'name', 'roles']
		);

		$data[0]['token'] = $session->getId();

		return $this->json($data);

	}

	/**
	 * @Rest\Get("/roles")
	 *
	 */
	public function getRoles(UserService $userService)
	{
		return $this->json($userService->getAvailableRoles($this->getUser()));
	}

}
