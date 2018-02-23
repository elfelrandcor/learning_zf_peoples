<?php
/**
 * @author Juriy Panasevich <elfelrandcor@gmail.com>
 */

namespace Rating\Factory;


use Application\Model\UserTable;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Rating\Model\RatingTable;
use Rating\Service\RatingService;
use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

class RatingServiceFactory implements FactoryInterface {

    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @param  null|array $options
     * @return RatingService
     * @throws \RuntimeException
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var UserTable $userTable */
        $userTable = $container->get(UserTable::class);
        /** @var AuthenticationService $identity */
        $identity = $container->get(AuthenticationService::class);

        return new RatingService($container->get(RatingTable::class), $identity->getIdentity() ? $userTable->getUser($identity->getIdentity()) : null);
    }
}