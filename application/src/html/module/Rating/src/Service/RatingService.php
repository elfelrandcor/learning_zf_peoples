<?php
/**
 * @author Juriy Panasevich <elfelrandcor@gmail.com>
 */

namespace Rating\Service;


use Application\Model\User;
use Rating\Model\RatingTable;
use Zend\Db\ResultSet\ResultSet;

class RatingService {

    private $user;
    private $table;

    public function __construct(RatingTable $table, User $user = null) {
        $this->table = $table;
        $this->user = $user;
    }

    /**
     * @param User $receiver
     * @return null|string
     */
    public function getVote(User $receiver) {
        if (!$this->user) {
            return null;
        }
        return $this->table->getVote($this->user, $receiver);
    }

    /**
     * @param User $user
     * @return ResultSet
     */
    public function getHistory(User $user): ResultSet {
        return $this->table->getForUser($user->id);
    }

    /**
     * @param User $receiver
     * @return RatingService
     * @throws \Zend\Db\Sql\Exception\InvalidArgumentException
     * @throws \RuntimeException
     */
    public function add(User $receiver) {
        if (!$this->user || $this->user->id == $receiver->id) {
            return $this;
        }
        $this->table->setPlusDirection($this->user, $receiver);
        return $this;
    }

    /**
     * @param User $receiver
     * @return RatingService
     * @throws \Zend\Db\Sql\Exception\InvalidArgumentException
     * @throws \RuntimeException
     */
    public function sub(User $receiver) {
        if (!$this->user || $this->user->id == $receiver->id) {
            return $this;
        }
        $this->table->setMinusDirection($this->user, $receiver);
        return $this;
    }
}