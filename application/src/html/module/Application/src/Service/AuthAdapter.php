<?php
/**
 * @author Juriy Panasevich <elfelrandcor@gmail.com>
 */

namespace Application\Service;

use Application\Model\UserTable;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;

class AuthAdapter implements AdapterInterface {

    /**
     * Password
     * @var string
     */
    private $password;

    private $userName;

    private $table;

    public function __construct(UserTable $table) {
        $this->table = $table;
    }

    /**
     * Performs an authentication attempt
     *
     * @return Result
     * @throws \RuntimeException
     * @throws \Zend\Authentication\Adapter\Exception\ExceptionInterface If authentication cannot be performed
     */
    public function authenticate(): Result {
        $user = $this->table->getUserByName($this->userName);

        if ($user === null) {
            return new Result(
                Result::FAILURE_IDENTITY_NOT_FOUND,
                null,
                ['Invalid credentials.']);
        }

        if ($this->password === $user->password) {
            return new Result(
                Result::SUCCESS,
                $user->id,
                ['Authenticated successfully.']);
        }
        return new Result(
            Result::FAILURE_CREDENTIAL_INVALID,
            null,
            ['Invalid credentials.']);
    }

    /**
     * @param mixed $userName
     * @return AuthAdapter
     */
    public function setUserName($userName): AuthAdapter {
        $this->userName = $userName;
        return $this;
    }

    /**
     * @param string $password
     * @return AuthAdapter
     */
    public function setPassword(string $password): AuthAdapter {
        $this->password = $password;
        return $this;
    }
}