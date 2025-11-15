<?php

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/AuthorsDao.php';

class AuthorsService extends BaseService
{
    public function __construct()
    {
        parent::__construct(new AuthorsDao());
    }

    public function get_author_by_name($name)
    {
        return $this->dao->getAuthorByName($name);
    }

    private function validate($entity)
    {
        if (!isset($entity['name']) || trim($entity['name']) === '') {
            throw new InvalidArgumentException('name is required');
        }
    }

    public function add($entity)
    {
        $this->validate($entity);
        $entity['created_at'] = date('Y-m-d');
        return parent::add($entity);
    }

    public function update($entity, $id, $id_column = "id")
    {
        $this->validate($entity);
        return parent::update($entity, $id, $id_column);
    }
}

?>