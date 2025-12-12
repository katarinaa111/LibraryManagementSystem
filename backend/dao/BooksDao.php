<?php
require_once 'BaseDao.php';

class BooksDao extends BaseDao {
    public function __construct()
    {
        parent::__construct("books");
    }

    public function get_all()
    {
        $query = "SELECT b.*, a.name AS author_name, c.name AS category_name FROM books b LEFT JOIN authors a ON b.author_id = a.id LEFT JOIN categories c ON b.category_id = c.id";
        return $this->query($query, []);
    }

    public function get_by_id($id)
    {
        $query = "SELECT b.*, a.name AS author_name, c.name AS category_name FROM books b LEFT JOIN authors a ON b.author_id = a.id LEFT JOIN categories c ON b.category_id = c.id WHERE b.id = :id";
        return $this->query_unique($query, ['id' => $id]);
    }

    public function getBooksByAuthorId($author_id) {
        $query = "SELECT b.*, a.name AS author_name, c.name AS category_name FROM books b LEFT JOIN authors a ON b.author_id = a.id LEFT JOIN categories c ON b.category_id = c.id WHERE b.author_id = :author_id";
        return $this->query($query, ['author_id' => $author_id]);
    }

    public function getBooksByCategoryId($category_id) {
        $query = "SELECT b.*, a.name AS author_name, c.name AS category_name FROM books b LEFT JOIN authors a ON b.author_id = a.id LEFT JOIN categories c ON b.category_id = c.id WHERE b.category_id = :category_id";
        return $this->query($query, ['category_id' => $category_id]);
    }
}
?>
