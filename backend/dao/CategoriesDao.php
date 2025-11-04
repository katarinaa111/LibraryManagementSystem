<?php
require_once 'BaseDao.php';

class CategoriesDao extends BaseDao {
    public function __construct()
    {
        parent::__construct("categories");
    }

    public function getCategoryByName($name) {
        $query = "SELECT * FROM categories WHERE name = :name";
        return $this->query_unique($query, ['name' => $name]);
    }
}
?>
