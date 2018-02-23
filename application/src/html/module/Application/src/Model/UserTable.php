<?php
/**
 * @author Juriy Panasevich <elfelrandcor@gmail.com>
 */

namespace Application\Model;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;

class UserTable {

    private $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll() {
        return $this->tableGateway->select();
    }

    /**
     * @param $id
     * @return mixed
     * @throws \RuntimeException
     */
    public function getUser($id): User {
        $id = (int)$id;
        /** @var \Zend\Db\ResultSet\ResultSet $rowset */
        $rowset = $this->tableGateway->select(['id' => $id]);
        $row = $rowset->current();
        if (!$row) {
            throw new RuntimeException(sprintf(
                'Could not find user with identifier %d',
                $id
            ));
        }

        return $row;
    }

    /**
     * @param string $name
     * @return User
     * @throws \RuntimeException
     */
    public function getUserByName(string $name): User {
        /** @var \Zend\Db\ResultSet\ResultSet $rowset */
        $rowset = $this->tableGateway->select(['name' => $name]);
        $row = $rowset->current();
        if (!$row) {
            throw new RuntimeException(sprintf(
                'Could not find user with name %d',
                $name
            ));
        }
        return $row;
    }

    /**
     * @param User $user
     * @return User
     * @throws \RuntimeException
     */
    public function saveUser(User $user): User {
        $data = [
            'name' => $user->name,
            'password' => $user->password,
            'sex' => $user->sex,
            'photo' => $user->photo,
            'rating' => $user->rating,
        ];

        $id = (int)$user->id;

        if ($id === 0) {
            $this->tableGateway->insert($data);
            return $this->getUserByName($user->name);
        }

        if (!$this->getUser($id)) {
            throw new RuntimeException(sprintf(
                'Cannot update user with identifier %d; does not exist',
                $id
            ));
        }

        $this->tableGateway->update($data, ['id' => $id]);
        return $this->getUserByName($user->name);
    }

    public function deleteUser($id) {
        $this->tableGateway->delete(['id' => (int)$id]);
    }
}