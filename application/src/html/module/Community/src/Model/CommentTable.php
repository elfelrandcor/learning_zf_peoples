<?php
/**
 * @author Juriy Panasevich <elfelrandcor@gmail.com>
 */

namespace Community\Model;


use Application\Model\User;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\TableGateway\TableGatewayInterface;

class CommentTable {

    /** @var AbstractTableGateway */
    private $tableGateway;
    /** @var User */
    private $user;


    public function __construct(TableGatewayInterface $tableGateway, User $user = null) {
        $this->tableGateway = $tableGateway;
        $this->user = $user;
    }

    /**
     * @param $id
     * @return ResultSet
     */
    public function getForUser($id): ResultSet {
        $id = (int)$id;
        /** @var ResultSet $rowset */
        return $this->tableGateway->select(['userId' => $id]);
    }

    /**
     * @param User $transmitter
     * @param User $receiver
     * @return array|\ArrayObject|null
     */
    public function getCommentsModel(User $transmitter, User $receiver) {
        /** @var ResultSet $rowset */
        $rowSet = $this->tableGateway->select([
            'fromUserId' => $transmitter->id,
            'userId' => $receiver->id,
        ]);

        return $rowSet->current();
    }

    /**
     * @param User $receiver
     * @param $text
     * @return CommentTable
     */
    public function add(User $receiver, $text): CommentTable {
        if (!$this->user || !$text) {
            return $this;
        }
        $data = [
            'userId' => $receiver->id,
            'fromUserId' => $this->user->id,
            'text' => $text,
            'dateCreate' => (new \DateTime())->format('Y-m-d H:i:s'),
        ];
        $this->tableGateway->insert($data);
        return $this;
    }
}