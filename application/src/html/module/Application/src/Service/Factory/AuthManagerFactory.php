<?php
/**
 * @author Juriy Panasevich <elfelrandcor@gmail.com>
 */

namespace Application\Service\Factory;


use Application\Service\AuthManager;
use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Zend\Session\SessionManager;

class AuthManagerFactory implements FactoryInterface {

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return AuthManager
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        return new AuthManager($container->get(AuthenticationService::class), $container->get(SessionManager::class));
    }
}