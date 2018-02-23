<?php
/**
 * @author Juriy Panasevich <elfelrandcor@gmail.com>
 */

namespace Application\Service;


use Application\Model\UserTable;
use Zend\Authentication\AuthenticationService;

class CurrentUserSetter {

    public function setCurrentUser($controller, $container) {
        $auth = $container->get(AuthenticationService::class);
        $user = null;

        if ($id = $auth->getIdentity()) {
            $user = $container->get(UserTable::class)->getUser($id);
            if (method_exists($controller, 'setCurrentUser')) {
                $controller->setCurrentUser($user);
            }
        }
        return $this;
    }
}