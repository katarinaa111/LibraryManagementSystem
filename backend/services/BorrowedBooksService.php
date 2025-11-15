<?php

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/BorrowedBooksDao.php';

class BorrowedBooksService extends BaseService
{
    public function __construct()
    {
        parent::__construct(new BorrowedBooksDao());
    }

    public function get_borrowed_by_user_id($user_id)
    {
        return $this->dao->getBorrowedBooksByUserId($user_id);
    }

    public function get_borrowed_by_book_id($book_id)
    {
        return $this->dao->getBorrowedBooksByBookId($book_id);
    }

    private function validate($entity)
    {
        if (!isset($entity['user_id']) || !is_numeric($entity['user_id'])) {
            throw new InvalidArgumentException('user_id is required');
        }
        if (!isset($entity['book_id']) || !is_numeric($entity['book_id'])) {
            throw new InvalidArgumentException('book_id is required');
        }
        if (!isset($entity['borrowed_date']) || !strtotime($entity['borrowed_date'])) {
            throw new InvalidArgumentException('borrowed_date is required');
        }
        if (!isset($entity['supposed_return_date']) || !strtotime($entity['supposed_return_date'])) {
            throw new InvalidArgumentException('supposed_return_date is required');
        }
        $usersDao = new UserDao();
        $booksDao = new BooksDao();
        if (!$usersDao->getById($entity['user_id'])) {
            throw new InvalidArgumentException('user_id not found');
        }
        if (!$booksDao->getById($entity['book_id'])) {
            throw new InvalidArgumentException('book_id not found');
        }
        $b = strtotime($entity['borrowed_date']);
        $s = strtotime($entity['supposed_return_date']);
        if ($s <= $b) {
            throw new InvalidArgumentException('supposed_return_date must be after borrowed_date');
        }
        if (isset($entity['returned_date']) && $entity['returned_date'] !== null) {
            $r = strtotime($entity['returned_date']);
            if (!$r || $r < $b) {
                throw new InvalidArgumentException('returned_date must be after borrowed_date');
            }
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