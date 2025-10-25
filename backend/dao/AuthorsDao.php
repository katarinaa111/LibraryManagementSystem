<?php
require_once 'BaseDao.php';

class AuthorsDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct("authors");
    }

    public function getAuthorByName($name)
    {
        $query = "SELECT * FROM authors WHERE name = :name";
        return $this->query_unique($query, ['name' => $name]);
    }
}
?>