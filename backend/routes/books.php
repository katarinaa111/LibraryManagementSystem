<?php

require_once __DIR__ . '/../services/BooksService.php';

$booksService = new BooksService();

/**
 * @OA\Get(
 *   path="/books",
 *   tags={"books"},
 *   summary="List books",
 *   @OA\Response(response=200, description="OK")
 * )
 */
Flight::route('GET /books', function () use ($booksService) {
    Flight::json($booksService->get_all());
});

/**
 * @OA\Get(
 *   path="/books/{id}",
 *   tags={"books"},
 *   summary="Get book by ID",
 *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\Response(response=200, description="OK")
 * )
 */
Flight::route('GET /books/@id', function ($id) use ($booksService) {
    Flight::json($booksService->get_by_id($id));
});

/**
 * @OA\Post(
 *   path="/books",
 *   tags={"books"},
 *   summary="Create book",
 *   @OA\RequestBody(required=true, content={
 *     "application/json": {
 *       "schema": {
 *         "type": "object",
 *         "properties": {
 *           "title": {"type": "string"},
 *           "author_id": {"type": "integer"},
 *           "category_id": {"type": "integer"}
 *         },
 *         "required": {"title", "author_id", "category_id"}
 *       }
 *     }
 *   }),
 *   @OA\Response(response=201, description="Created")
 * )
 */
Flight::route('POST /books', function () use ($booksService) {
    $data = json_decode(Flight::request()->getBody(), true);
    try {
        Flight::json($booksService->add($data), 201);
    } catch (Throwable $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Put(
 *   path="/books/{id}",
 *   tags={"books"},
 *   summary="Update book",
 *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\RequestBody(required=true, content={
 *     "application/json": {
 *       "schema": {
 *         "type": "object",
 *         "properties": {
 *           "title": {"type": "string"},
 *           "author_id": {"type": "integer"},
 *           "category_id": {"type": "integer"}
 *         },
 *         "required": {"title", "author_id", "category_id"}
 *       }
 *     }
 *   }),
 *   @OA\Response(response=200, description="OK")
 * )
 */
Flight::route('PUT /books/@id', function ($id) use ($booksService) {
    $data = json_decode(Flight::request()->getBody(), true);
    try {
        Flight::json($booksService->update($data, $id));
    } catch (Throwable $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Delete(
 *   path="/books/{id}",
 *   tags={"books"},
 *   summary="Delete book",
 *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\Response(response=200, description="OK")
 * )
 */
Flight::route('DELETE /books/@id', function ($id) use ($booksService) {
    Flight::json(['success' => $booksService->delete($id)]);
});

/**
 * @OA\Get(
 *   path="/books/by-author/{author_id}",
 *   tags={"books"},
 *   summary="List books by author",
 *   @OA\Parameter(name="author_id", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\Response(response=200, description="OK")
 * )
 */
Flight::route('GET /books/by-author/@author_id', function ($author_id) use ($booksService) {
    Flight::json($booksService->get_books_by_author_id($author_id));
});

/**
 * @OA\Get(
 *   path="/books/by-category/{category_id}",
 *   tags={"books"},
 *   summary="List books by category",
 *   @OA\Parameter(name="category_id", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\Response(response=200, description="OK")
 * )
 */
Flight::route('GET /books/by-category/@category_id', function ($category_id) use ($booksService) {
    Flight::json($booksService->get_books_by_category_id($category_id));
});

?>