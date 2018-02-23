<?php
/**
 * @author Juriy Panasevich <elfelrandcor@gmail.com>
 */

namespace Rating\Model;


use Application\Model\User;
use Application\Model\UserTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\TableGatewayInterface;

class RatingTable {

    /** @var AbstractTableGateway */
    private $tableGateway;
    /** @var UserTable */
    private $userTable;

    const DIRECTION_PLUS = 1;

    const DIRECTION_MINUS = -1;

    public function __construct(TableGatewayInterface $tableGateway, UserTable $userTable) {
        $this->tableGateway = $tableGateway;
        $this->userTable = $userTable;
    }

    /**
     * @return ResultSet
     */
    public function fetchAll(): ResultSet {
        return $this->tableGateway->select();
    }

    public function getForUser($id): ResultSet {
        $id = (int)$id;
        /** @var ResultSet $rowset */
        return $this->tableGateway->select(['userId' => $id]);
    }

    public function getRatingModel(User $transmitter, User $receiver) {
        /** @var ResultSet $rowset */
        $rowSet = $this->tableGateway->select([
            'fromUserId' => $transmitter->id,
            'userId' => $receiver->id,
        ]);

        return $rowSet->current();
    }

    /**
     * @param User $transmitter
     * @param User $receiver
     * @return null|string
     */
    public function getVote(User $transmitter, User $receiver) {
        if (!$row = $this->getRatingModel($transmitter, $receiver)) {
            return null;
        }
        return $row->direction;
    }

    /**
     * @param User $transmitter
     * @param User $receiver
     * @return RatingTable
     * @throws \RuntimeException
     * @throws \Zend\Db\Sql\Exception\InvalidArgumentException
     */
    public function setPlusDirection(User $transmitter, User $receiver) {
        $this->vote($transmitter, $receiver, self::DIRECTION_PLUS);
        return $this;
    }

    /**
     * @param User $transmitter
     * @param User $receiver
     * @return RatingTable
     * @throws \RuntimeException
     * @throws \Zend\Db\Sql\Exception\InvalidArgumentException
     */
    public function setMinusDirection(User $transmitter, User $receiver) {
        $this->vote($transmitter, $receiver, self::DIRECTION_MINUS);
        return $this;
    }

    /**
     * @param User $transmitter
     * @param User $receiver
     * @param $direction
     * @return RatingTable
     * @throws \RuntimeException
     * @throws \Zend\Db\TableGateway\Exception\InvalidArgumentException
     * @throws \Zend\Db\Sql\Exception\InvalidArgumentException
     */
    private function vote(User $transmitter, User $receiver, $direction) {
        $data = [
            'userId' => $receiver->id,
            'fromUserId' => $transmitter->id,
            'direction' => $direction,
            'dateCreate' => (new \DateTime())->format('Y-m-d H:i:s'),
        ];

        if (!$row = $this->getRatingModel($transmitter, $receiver)) {
            $this->tableGateway->insert($data);
        } else {
            $this->tableGateway->update($data, ['id' => $row->id]);
        }

        $this->recalculate($receiver);
        return $this;
    }

    /**
     * @param User $user
     * @return RatingTable
     * @throws \RuntimeException
     * @throws \Zend\Db\TableGateway\Exception\InvalidArgumentException
     * @throws \Zend\Db\Sql\Exception\InvalidArgumentException
     */
    private function recalculate(User $user): RatingTable {
        $gateway = new TableGateway('votes', $this->tableGateway->getAdapter());

        $rowset = $gateway->select(function (Select $select) use ($user) {
            $select->columns([
                'rating' => new Expression('SUM(direction)')
            ]);
            $select->where(['userId' => $user->id]);
        });
        if (!$row = $rowset->current()) {
            throw new \RuntimeException('No votes');
        }
        $user->rating = $row->rating;
        $this->userTable->saveUser($user);
        return $this;
    }
}