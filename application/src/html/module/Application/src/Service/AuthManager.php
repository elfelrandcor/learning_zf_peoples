<?php
/**
 * @author Juriy Panasevich <elfelrandcor@gmail.com>
 */

namespace Application\Service;


use Zend\Session\SessionManager;
use Zend\Authentication\Result;

class AuthManager {
    /**
     * Authentication service.
     * @var \Zend\Authentication\AuthenticationService
     */
    private $authService;

    /**
     * Session manager.
     * @var SessionManager
     */
    private $sessionManager;


    /**
     * Constructs the service.
     * @param $authService
     * @param $sessionManager
     */
    public function __construct($authService, $sessionManager) {
        $this->authService = $authService;
        $this->sessionManager = $sessionManager;
    }

    /**
     * @param $userName
     * @param $password
     * @return Result
     * @throws \RuntimeException
     */
    public function login($userName, $password) {
        if ($this->authService->getIdentity() != null) {
            throw new \RuntimeException('Already logged in');
        }

        /** @var AuthAdapter $authAdapter */
        $authAdapter = $this->authService->getAdapter();
        $authAdapter->setUserName($userName);
        $authAdapter->setPassword($password);
        $result = $this->authService->authenticate();

        if ($result->getCode() == Result::SUCCESS) {
            $this->sessionManager->rememberMe(60 * 60 * 24 * 30);
        }

        return $result;
    }

    /**
     * Performs user logout.
     * @throws \Exception
     */
    public function logout() {
        if ($this->authService->getIdentity() == null) {
            throw new \Exception('The user is not logged in');
        }

        $this->authService->clearIdentity();
    }
}