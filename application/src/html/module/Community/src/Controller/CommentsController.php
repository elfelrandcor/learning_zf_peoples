<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Community\Controller;

use Application\Model\UserTable;
use Community\Form\CommentForm;
use Community\Model\Comment;
use Community\Service\CommentsService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class CommentsController extends AbstractActionController {

    private $table, $service;

    public function __construct(UserTable $table, CommentsService $service) {
        $this->table = $table;
        $this->service = $service;
    }

    /**
     * @return JsonModel
     * @throws \Zend\Mvc\Exception\RuntimeException
     * @throws \Zend\Form\Exception\DomainException
     * @throws \RuntimeException
     */
    public function addAction() {
        if (!$user = $this->table->getUser((int)$this->params()->fromRoute('id', 0))) {
            throw new \RuntimeException('User not found');
        }
        $form = new CommentForm();
        $data = $this->params()->fromPost();
        $form->setData($data);

        if (!$form->isValid()) {
            return new JsonModel(['form' => $form]);
        }
        $data = $form->getData();

        $this->service->add($user, $data['text']);
        return new JsonModel([$user->id => $user->rating]);
    }

    /**
     * @return ViewModel
     * @throws \RuntimeException
     */
    public function userAction(): ViewModel {
        if (!$user = $this->table->getUser((int)$this->params()->fromRoute('id', 0))) {
            throw new \RuntimeException('User not found');
        }
        $list = [];
        /** @var Comment $comment */
        foreach ($this->service->getForUser($user) as $comment) {
            $comment->user = $this->table->getUser($comment->fromUserId);
            $list[] = $comment;
        }

        $form = new CommentForm();

        $viewModel = new ViewModel([
            'id' => $user->id,
            'form' => $form,
            'list' => $list,
            'currentUser' => $this->identity() ? $this->table->getUser($this->identity()) : null,
        ]);
        $viewModel->setTerminal(true);
        return $viewModel;
    }
}
