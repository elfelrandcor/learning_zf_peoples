<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Rating\Controller;

use Application\Model\User;
use Application\Model\UserTable;
use Rating\Model\Rating;
use Rating\Service\RatingService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class RatingController extends AbstractActionController {

    private $table, $service;

    public function __construct(UserTable $table, RatingService $service) {
        $this->table = $table;
        $this->service = $service;
    }

    /**
     * @return ViewModel
     * @throws \RuntimeException
     */
    public function indexAction(): ViewModel {
        $list = [];
        /** @var User $user */
        foreach ($this->table->fetchAll() as $user) {
            $user->vote = $this->service->getVote($user);
            $list[] = $user;
        }
        $viewModel = new ViewModel([
            'list' => $list,
            'currentUser' => $this->identity() ? $this->table->getUser($this->identity()) : null,
        ]);
        $viewModel->setTerminal(true);
        return $viewModel;
    }

    public function buttonsAction() {
        if (!$user = $this->table->getUser((int)$this->params()->fromRoute('id', 0))) {
            throw new \RuntimeException('User not found');
        }
        $user->vote = $this->service->getVote($user);

        $viewModel = new ViewModel([
            'user' => $user,
            'currentUser' => $this->identity() ? $this->table->getUser($this->identity()) : null,
        ]);
        $viewModel->setTerminal(true);
        return $viewModel;
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
        /** @var Rating $rating */
        foreach ($this->service->getHistory($user) as $rating) {
            $rating->fromUser = $this->table->getUser($rating->fromUserId);
            $list[] = $rating;
        }

        $viewModel = new ViewModel([
            'list' => $list,
            'currentUser' => $this->identity() ? $this->table->getUser($this->identity()) : null,
        ]);
        $viewModel->setTerminal(true);
        return $viewModel;
    }

    /**
     * @return JsonModel
     * @throws \RuntimeException
     */
    public function upAction() {
        if (!$user = $this->table->getUser((int)$this->params()->fromRoute('id', 0))) {
            throw new \RuntimeException('User not found');
        }

        $this->service->add($user);
        return new JsonModel([$user->id => $user->rating]);
    }

    /**
     * @return JsonModel
     * @throws \RuntimeException
     */
    public function downAction() {
        if (!$user = $this->table->getUser((int)$this->params()->fromRoute('id', 0))) {
            throw new \RuntimeException('User not found');
        }
        $this->service->sub($user);
        return new JsonModel([$user->id => $user->rating]);
    }
}
