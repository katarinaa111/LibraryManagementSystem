<?php
require_once 'BaseDao.php';

class BooksDao extends BaseDao {
    public function __construct()
    {
        parent::__construct("books");
    }

    public function getBooksByAuthorId($author_id) {
        $query = "SELECT * FROM books WHERE author_id = :author_id";
        return $this->query($query, ['author_id' => $author_id]);
    }

    public function getBooksByCategoryId($category_id) {
        $query = "SELECT * FROM books WHERE category_id = :category_id";
        return $this->query($query, ['category_id' => $category_id]);
    }
}
?>
