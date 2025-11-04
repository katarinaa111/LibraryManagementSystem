<?php
require_once 'BaseDao.php';

class BorrowedBooksDao extends BaseDao {
    public function __construct()
    {
        parent::__construct("borrowedbooks");
    }

    public function getBorrowedBooksByUserId($user_id) {
        $query = "SELECT * FROM borrowedbooks WHERE user_id = :user_id";
        return $this->query($query, ['user_id' => $user_id]);
    }

    public function getBorrowedBooksByBookId($book_id) {
        $query = "SELECT * FROM borrowedbooks WHERE book_id = :book_id";
        return $this->query($query, ['book_id' => $book_id]);
    }
}
?>
