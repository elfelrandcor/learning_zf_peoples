<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Application\Form\UserEditForm;
use Application\Form\UserRegisterForm;
use Application\Model\User;
use Application\Model\UserTable;
use Application\Service\AuthManager;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\Identical;
use Zend\View\Model\ViewModel;

class UserController extends AbstractActionController {

    private $table, $dbAdapter, $authManager;
    private $currentUser;

    public function __construct(UserTable $table, AdapterInterface $adapter, AuthManager $authManager) {
        $this->table = $table;
        $this->dbAdapter = $adapter;
        $this->authManager = $authManager;
    }

    /**
     * @return ViewModel
     */
    public function indexAction(): ViewModel {
        $this->layout()->setVariable('currentUser', $this->currentUser);

        return new ViewModel([
            'users' => $this->table->fetchAll(),
        ]);
    }

    /**
     * @return array|\Zend\Http\Response
     * @throws \Zend\InputFilter\Exception\InvalidArgumentException
     * @throws \Zend\Validator\Exception\InvalidArgumentException
     * @throws \RuntimeException
     * @throws \Zend\Mvc\Exception\DomainException
     * @throws \Zend\Form\Exception\DomainException
     * @throws \Zend\Form\Exception\InvalidArgumentException
     */
    public function registerAction() {
        $this->layout()->setVariable('currentUser', $this->currentUser);

        if ($this->currentUser) {
            return $this->redirect()->toRoute('home');
        }
        $form = new UserRegisterForm();
        $form->get('submit')->setValue('Sign Up');

        /** @var Request $request */
        $request = $this->getRequest();

        if (!$request->isPost()) {
            return ['form' => $form];
        }

        $post = array_merge_recursive(
            $request->getPost()->toArray(),
            $request->getFiles()->toArray()
        );

        $user = new User($this->dbAdapter);
        $inputFilter =
            $user->getInputFilter()
                 ->add([
                     'name' => 'confirm_password',
                     'filters' => [],
                     'validators' => [
                         [
                             'name' => Identical::class,
                             'options' => [
                                 'token' => 'password',
                             ],
                         ],
                     ],
                 ])
        ;
        $form->setInputFilter($inputFilter);
        $form->setData($post);

        if (!$form->isValid()) {
            return ['form' => $form];
        }
        try {
            $user->exchangeArray($form->getData());
            //todo Manipulate
            if (\is_array($user->photo)) {
                $user->photo = basename($user->photo['tmp_name']);
            }
            $user = $this->table->saveUser($user);
            $this->authManager->login($user->name, $user->password);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('home', ['action' => 'index']);
        }
        return $this->redirect()->toRoute('user', ['action' => 'show', 'id' => $user->id]);
    }

    /**
     * @return array|\Zend\Http\Response
     * @throws \Zend\Validator\Exception\InvalidArgumentException
     * @throws \Zend\Mvc\Exception\RuntimeException
     * @throws \Zend\Mvc\Exception\DomainException
     * @throws \Zend\Form\Exception\InvalidArgumentException
     * @throws \RuntimeException
     */
    public function editAction() {
        $id = (int)$this->params()->fromRoute('id', 0);
        if ($id != $this->currentUser->id) {
            return $this->redirect()->toRoute('home', ['action' => 'index']);
        }
        if (0 === $id) {
            return $this->redirect()->toRoute('user', ['action' => 'register']);
        }

        try {
            $user = $this->table->getUser($id);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('home', ['action' => 'index']);
        }
        $this->layout()->setVariable('currentUser', $this->currentUser);

        $form = new UserEditForm();
        $form->bind($user);
        $form->get('submit')->setAttribute('value', 'Save');

        $form->getInputFilter()->remove('id')->remove('name')->remove('password');

        $request = $this->getRequest();
        $viewData = ['id' => $id, 'form' => $form];

        if (!$request->isPost()) {
            return $viewData;
        }
        $post = array_merge_recursive(
            $request->getPost()->toArray(),
            $request->getFiles()->toArray()
        );

        $form->setInputFilter($user->getInputFilter());
        $form->setData($post);

        if (!$form->isValid()) {
            return $viewData;
        }

        try {
            if (\is_array($user->photo)) {
                $user->photo = basename($user->photo['tmp_name']);
            }
            $this->table->saveUser($user);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('home', ['action' => 'index']);
        }

        return $this->redirect()->toRoute('user', ['action' => 'show', 'id' => $id]);
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     * @throws \Zend\Mvc\Exception\DomainException
     * @throws \Zend\Mvc\Exception\RuntimeException
     */
    public function showAction() {
        $this->layout()->setVariable('currentUser', $this->currentUser);
        $id = (int)$this->params()->fromRoute('id', 0);

        if (0 === $id) {
            return $this->redirect()->toRoute('user', ['action' => 'register']);
        }

        try {
            $user = $this->table->getUser($id);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('home', ['action' => 'index']);
        }

        return new ViewModel([
            'user' => $user,
            'currentUser' => $this->currentUser,
        ]);
    }

    /**
     * @param mixed $currentUser
     * @return UserController
     */
    public function setCurrentUser(User $currentUser) {
        $this->currentUser = $currentUser;
        return $this;
    }
}
