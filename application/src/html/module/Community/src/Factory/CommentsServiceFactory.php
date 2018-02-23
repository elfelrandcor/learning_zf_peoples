<?php
/**
 * @author Juriy Panasevich <elfelrandcor@gmail.com>
 */

namespace Community\Factory;


use Application\Model\UserTable;
use Community\Model\CommentTable;
use Community\Service\CommentsService;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

class CommentsServiceFactory implements FactoryInterface {

    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @param  null|array $options
     * @return CommentsService
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

        return new CommentsService($container->get(CommentTable::class), $identity->getIdentity() ? $userTable->getUser($identity->getIdentity()) : null);
    }
}