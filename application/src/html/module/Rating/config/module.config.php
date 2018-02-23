<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Rating;

use Application\Model\UserTable;
use Rating\Factory\RatingServiceFactory;
use Rating\Model\RatingTable;
use Rating\Service\RatingService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'rating' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/rating[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\RatingController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],
    'service_manager' => [
        'factories' => [
            RatingService::class => RatingServiceFactory::class,
            RatingTable::class => function($container) {
                $tableGateway = $container->get(Model\RatingTableGateway::class);
                return new Model\RatingTable($tableGateway, $container->get(UserTable::class));
            },
            Model\RatingTableGateway::class => function ($container) {
                $dbAdapter = $container->get(AdapterInterface::class);
                $resultSetPrototype = new ResultSet();
                $resultSetPrototype->setArrayObjectPrototype(new Model\Rating($dbAdapter));
                return new TableGateway('votes', $dbAdapter, null, $resultSetPrototype);
            },
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\RatingController::class => function($container) {
                return new Controller\RatingController(
                    $container->get(UserTable::class),
                    $container->get(RatingService::class)
                );
            },
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'rating' => __DIR__ . '/../view',
        ],
    ],
];
