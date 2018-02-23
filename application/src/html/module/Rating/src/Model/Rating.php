<?php
/**
 * @author Juriy Panasevich <elfelrandcor@gmail.com>
 */

namespace Rating\Model;


use Application\Model\User;

class Rating {

    public $id, $userId, $fromUserId, $direction, $dateCreate;
    /** @var User */
    public $fromUser;

    public function exchangeArray(array $data) {
        $this->id = !empty($data['id']) ? $data['id'] : null;
        $this->userId = !empty($data['userId']) ? $data['userId'] : null;
        $this->fromUserId = !empty($data['fromUserId']) ? $data['fromUserId'] : null;
        $this->direction = !empty($data['direction']) ? $data['direction'] : null;
        $this->dateCreate = !empty($data['dateCreate']) ? $data['dateCreate'] : null;
    }

    public function isPositive() {
        return $this->direction == RatingTable::DIRECTION_PLUS;
    }
}