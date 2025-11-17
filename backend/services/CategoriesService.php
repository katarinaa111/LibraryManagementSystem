<?php

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/CategoriesDao.php';

class CategoriesService extends BaseService
{
    public function __construct()
    {
        parent::__construct(new CategoriesDao());
    }

    public function get_category_by_name($name)
    {
        return $this->dao->getCategoryByName($name);
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
        return parent::add($entity);
    }

    public function update($entity, $id, $id_column = "id")
    {
        $this->validate($entity);
        return parent::update($entity, $id, $id_column);
    }
}

?>