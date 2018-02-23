<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Application\Model\User;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Class IndexController
 * @package Application\Controller
 * @method identity
 */
class IndexController extends AbstractActionController {
    protected $table;
    protected $currentUser;

    /**
     * @return ViewModel
     */
    public function indexAction(): ViewModel {
        $this->layout()->setVariable('currentUser', $this->currentUser);
        return new ViewModel();
    }

    /**
     * @param User $currentUser
     * @return IndexController
     */
    public function setCurrentUser(User $currentUser): IndexController {
        $this->currentUser = $currentUser;
        return $this;
    }
}
