<?php

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/BooksDao.php';

class BooksService extends BaseService
{
    public function __construct()
    {
        parent::__construct(new BooksDao());
    }

    public function get_books_by_author_id($author_id)
    {
        return $this->dao->getBooksByAuthorId($author_id);
    }

    public function get_books_by_category_id($category_id)
    {
        return $this->dao->getBooksByCategoryId($category_id);
    }

    private function validate($entity)
    {
        if (!isset($entity['title']) || trim($entity['title']) === '') {
            throw new InvalidArgumentException('title is required');
        }
        if (!isset($entity['author_id']) || !is_numeric($entity['author_id'])) {
            throw new InvalidArgumentException('author_id is required');
        }
        if (!isset($entity['category_id']) || !is_numeric($entity['category_id'])) {
            throw new InvalidArgumentException('category_id is required');
        }
        $authorsDao = new AuthorsDao();
        $categoriesDao = new CategoriesDao();
        if (!$authorsDao->getById($entity['author_id'])) {
            throw new InvalidArgumentException('author_id not found');
        }
        if (!$categoriesDao->getById($entity['category_id'])) {
            throw new InvalidArgumentException('category_id not found');
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