<?php

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/UsersDao.php';

class UsersService extends BaseService
{
    public function __construct()
    {
        parent::__construct(new UserDao());
    }

    public function get_by_email($email)
    {
        return $this->dao->getByEmail($email);
    }

    public function get_users_by_role($role)
    {
        return $this->dao->getUsersByRole($role);
    }

    private function validate($entity, $is_update = false)
    {
        $roles = ['admin', 'member'];
        if (!isset($entity['username']) || trim($entity['username']) === '') {
            throw new InvalidArgumentException('username is required');
        }
        if (!isset($entity['email']) || !filter_var($entity['email'], FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('valid email is required');
        }
        if (!$is_update) {
            $existing = $this->dao->getByEmail($entity['email']);
            if ($existing) {
                throw new InvalidArgumentException('email already exists');
            }
        }
        if (!isset($entity['role']) || !in_array($entity['role'], $roles)) {
            throw new InvalidArgumentException('invalid role');
        }
    }

    public function add($entity)
    {
        $this->validate($entity, false);
        return parent::add($entity);
    }

    public function update($entity, $id, $id_column = "id")
    {
        $this->validate($entity, true);
        return parent::update($entity, $id, $id_column);
    }
}

?>