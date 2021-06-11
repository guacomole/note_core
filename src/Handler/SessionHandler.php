<?php


namespace App\Handler;


use App\Entity\User;
use App\Service\SessionService;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;

class SessionHandler extends PdoSessionHandler
{
	private ?SessionService $sessionService = null;
	private ?Security $security = null;
	private ?UserService $userService = null;
	private ?string $pdoOrDs;
	private ?array $options;

	/**
	 * SessionHandler constructor.
	 * @param null $pdoOrDsn
	 * @param array $options
	 * @param SessionService|null $sessionService
	 * @param UserService $userService
	 * @param Security|null $security
	 */
	public function __construct(string $pdoOrDsn, array $options, SessionService $sessionService, UserService $userService, Security $security = null)
	{
		$this->sessionService = $sessionService;
		$this->userService = $userService;
		$this->security = $security;

		return parent::__construct($pdoOrDsn, $options);
	}

	public function doWrite($sessionId, $data)
	{
		if (parent::doWrite($sessionId, $data))
		{
			$session = $this->sessionService->oneById($sessionId);
			$data = unserialize(str_replace('_sf2_attributes|' , '' , $data));

			if (isset($data['username'])) {
				$user = $this->userService->oneByLogin($data['username']);
			} else {
				$user = $this->security->getUser();
			}

			if ($session && $user) {
				$user->addSession($session);
				$this->userService->createOrUpdate($user);
				$this->sessionService->removeUnnecessarySessionByUser($user, $session);
			}
			return $session;
		}
		return true;
	}
}