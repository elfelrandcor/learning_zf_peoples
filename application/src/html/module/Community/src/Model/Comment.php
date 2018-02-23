<?php
/**
 * @author Juriy Panasevich <elfelrandcor@gmail.com>
 */

namespace Community\Model;


use Application\Model\User;

class Comment {

    public $id, $userId, $fromUserId, $text, $dateCreate;
    /** @var User */
    public $user;

    public function exchangeArray(array $data) {
        $this->id = !empty($data['id']) ? $data['id'] : null;
        $this->userId = !empty($data['userId']) ? $data['userId'] : null;
        $this->fromUserId = !empty($data['fromUserId']) ? $data['fromUserId'] : null;
        $this->text = !empty($data['text']) ? $data['text'] : null;
        $this->dateCreate = !empty($data['dateCreate']) ? $data['dateCreate'] : null;
    }
}