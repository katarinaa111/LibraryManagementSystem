<?php
require_once 'BaseDao.php';

class BorrowedBooksDao extends BaseDao {
    public function __construct()
    {
        parent::__construct("borrowedbooks");
    }

    public function get_all()
    {
        $query = "SELECT bb.*, u.username AS member_username, CONCAT(u.firstName, ' ', u.lastName) AS member_name, b.title AS book_title FROM borrowedbooks bb LEFT JOIN users u ON bb.user_id = u.id LEFT JOIN books b ON bb.book_id = b.id";
        return $this->query($query, []);
    }

    public function get_by_id($id)
    {
        $query = "SELECT bb.*, u.username AS member_username, CONCAT(u.firstName, ' ', u.lastName) AS member_name, b.title AS book_title FROM borrowedbooks bb LEFT JOIN users u ON bb.user_id = u.id LEFT JOIN books b ON bb.book_id = b.id WHERE bb.id = :id";
        return $this->query_unique($query, ['id' => $id]);
    }

    public function getBorrowedBooksByUserId($user_id) {
        $query = "SELECT bb.*, u.username AS member_username, CONCAT(u.firstName, ' ', u.lastName) AS member_name, b.title AS book_title FROM borrowedbooks bb LEFT JOIN users u ON bb.user_id = u.id LEFT JOIN books b ON bb.book_id = b.id WHERE bb.user_id = :user_id";
        return $this->query($query, ['user_id' => $user_id]);
    }

    public function getBorrowedBooksByBookId($book_id) {
        $query = "SELECT bb.*, u.username AS member_username, CONCAT(u.firstName, ' ', u.lastName) AS member_name, b.title AS book_title FROM borrowedbooks bb LEFT JOIN users u ON bb.user_id = u.id LEFT JOIN books b ON bb.book_id = b.id WHERE bb.book_id = :book_id";
        return $this->query($query, ['book_id' => $book_id]);
    }
}
?>
