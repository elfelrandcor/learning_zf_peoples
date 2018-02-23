<?php
/**
 * @author Juriy Panasevich <elfelrandcor@gmail.com>
 */

namespace Community\Service;


use Application\Model\User;
use Community\Model\CommentTable;
use Zend\Db\ResultSet\ResultSet;

class CommentsService {
    private $user;
    private $table;

    public function __construct(CommentTable $table, User $user = null) {
        $this->table = $table;
        $this->user = $user;
    }

    /**
     * @param User $user
     * @return ResultSet
     */
    public function getForUser(User $user): ResultSet {
        return $this->table->getForUser($user->id);
    }

    /**
     * @param User $receiver
     * @param string $text
     * @return CommentsService
     */
    public function add(User $receiver, string $text): CommentsService {
        if (!$this->user) {
            return $this;
        }
        $this->table->add($receiver, $text);
        return $this;
    }
}