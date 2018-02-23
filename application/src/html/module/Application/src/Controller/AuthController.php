<?php
/**
 * @author Juriy Panasevich <elfelrandcor@gmail.com>
 */

namespace Application\Controller;


use Application\Form\LoginForm;
use Application\Service\AuthManager;
use Zend\Authentication\Result;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AuthController extends AbstractActionController {

    protected $authService, $table, $manager;

    public function __construct(AuthManager $manager) {
        $this->manager = $manager;
    }

    /**
     * @return array|\Zend\Http\Response
     * @throws \Zend\Mvc\Exception\DomainException
     */
    public function indexAction() {
        return $this->redirect()->toRoute('login');
    }

    /**
     * @return array|\Zend\Http\Response|ViewModel
     * @throws \Exception
     */
    public function loginAction() {
        if ($this->identity()) {
            return $this->redirect()->toRoute('home');
        }
        $form = new LoginForm();

        if ($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);

            if (!$form->isValid()) {
                return ['form' => $form];
            }

            $data = $form->getData();

            $result = $this->manager->login($data['name'],
                $data['password']);

            if ($result->getCode() == Result::SUCCESS) {
                return $this->redirect()->toRoute('home');
            }
        }
        return new ViewModel(['form' => $form]);
    }

    /**
     * @return \Zend\Http\Response
     * @throws \Exception
     */
    public function logoutAction() {
        $this->manager->logout();
        return $this->redirect()->toRoute('login');
    }
}
