<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Community;

use Application\Model\UserTable;
use Community\Factory\CommentsServiceFactory;
use Community\Model\CommentTable;
use Community\Service\CommentsService;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'community' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/comments[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\CommentsController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\CommentsController::class => function($container) {
                return new Controller\CommentsController(
                    $container->get(UserTable::class),
                    $container->get(CommentsService::class)
                );
            },
        ],
    ],
    'service_manager' => [
        'factories' => [
            CommentsService::class => CommentsServiceFactory::class,
            CommentTable::class => function($container) {
                $tableGateway = $container->get(Model\CommentsTableGateway::class);
                $auth = $container->get(AuthenticationService::class);
                $identity = $auth->getIdentity();
                return new Model\CommentTable($tableGateway, $identity ? $container->get(UserTable::class)->getUser($identity) : null);
            },
            Model\CommentsTableGateway::class => function ($container) {
                $dbAdapter = $container->get(AdapterInterface::class);
                $resultSetPrototype = new ResultSet();
                $resultSetPrototype->setArrayObjectPrototype(new Model\Comment($dbAdapter));
                return new TableGateway('comments', $dbAdapter, null, $resultSetPrototype);
            },
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'community' => __DIR__ . '/../view',
        ],
    ],
];
