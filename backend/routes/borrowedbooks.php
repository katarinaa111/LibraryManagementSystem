<?php

require_once __DIR__ . '/../services/BorrowedBooksService.php';
require_once __DIR__ . '/../services/BooksService.php';
require_once __DIR__ . '/../data/Roles.php';

$borrowedBooksService = new BorrowedBooksService();
$booksService = new BooksService();

/**
 * @OA\Get(
 *   path="/borrowedbooks",
 *   tags={"borrowedbooks"},
 *   summary="List borrowed records",
 *   @OA\Response(response=200, description="OK")
 * )
 */
Flight::route('GET /borrowedbooks', function () use ($borrowedBooksService) {
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::MEMBER]);
    Flight::json($borrowedBooksService->get_all());
});

/**
 * @OA\Get(
 *   path="/borrowedbooks/{id}",
 *   tags={"borrowedbooks"},
 *   summary="Get borrowed record by ID",
 *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\Response(response=200, description="OK")
 * )
 */
Flight::route('GET /borrowedbooks/@id', function ($id) use ($borrowedBooksService) {
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::MEMBER]);
    Flight::json($borrowedBooksService->get_by_id($id));
});

/**
 * @OA\Post(
 *   path="/borrowedbooks",
 *   tags={"borrowedbooks"},
 *   summary="Create borrowed record",
 *   @OA\RequestBody(required=true, content={
 *     @OA\MediaType(
 *       mediaType="application/json",
 *       @OA\Schema(
 *         type="object",
 *         required={"user_id","book_id","borrowed_date","supposed_return_date"},
 *         @OA\Property(property="user_id", type="integer"),
 *         @OA\Property(property="book_id", type="integer"),
 *         @OA\Property(property="borrowed_date", type="string", format="date"),
 *         @OA\Property(property="supposed_return_date", type="string", format="date"),
 *         @OA\Property(property="returned_date", type="string", format="date")
 *       )
 *     )
 *   }),
 *   @OA\Response(response=200, description="Created")
 * )
 */
Flight::route('POST /borrowedbooks', function () use ($borrowedBooksService) {
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    $data = json_decode(Flight::request()->getBody(), true);
    try {
        Flight::json($borrowedBooksService->add($data), 200);
    } catch (Throwable $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Put(
 *   path="/borrowedbooks/{id}",
 *   tags={"borrowedbooks"},
 *   summary="Update borrowed record",
 *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\RequestBody(required=true, content={
 *     @OA\MediaType(
 *       mediaType="application/json",
 *       @OA\Schema(
 *         type="object",
 *         required={"user_id","book_id","borrowed_date","supposed_return_date"},
 *         @OA\Property(property="user_id", type="integer"),
 *         @OA\Property(property="book_id", type="integer"),
 *         @OA\Property(property="borrowed_date", type="string", format="date"),
 *         @OA\Property(property="supposed_return_date", type="string", format="date"),
 *         @OA\Property(property="returned_date", type="string", format="date")
 *       )
 *     )
 *   }),
 *   @OA\Response(response=200, description="OK")
 * )
 */
Flight::route('PUT /borrowedbooks/@id', function ($id) use ($borrowedBooksService) {
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    $data = json_decode(Flight::request()->getBody(), true);
    try {
        Flight::json($borrowedBooksService->update($data, $id));
    } catch (Throwable $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Delete(
 *   path="/borrowedbooks/{id}",
 *   tags={"borrowedbooks"},
 *   summary="Delete borrowed record",
 *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\Response(response=200, description="OK")
 * )
 */
Flight::route('DELETE /borrowedbooks/@id', function ($id) use ($borrowedBooksService) {
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    Flight::json(['success' => $borrowedBooksService->delete($id)]);
});

/**
 * @OA\Get(
 *   path="/borrowedbooks/by-user/{user_id}",
 *   tags={"borrowedbooks"},
 *   summary="List borrowed records by user",
 *   @OA\Parameter(name="user_id", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\Response(response=200, description="OK")
 * )
 */
Flight::route('GET /borrowedbooks/by-user/@user_id', function ($user_id) use ($borrowedBooksService) {
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::MEMBER]);
    Flight::json($borrowedBooksService->get_borrowed_by_user_id($user_id));
});

/**
 * @OA\Get(
 *   path="/borrowedbooks/by-book/{book_id}",
 *   tags={"borrowedbooks"},
 *   summary="List borrowed records by book",
 *   @OA\Parameter(name="book_id", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\Response(response=200, description="OK")
 * )
 */
Flight::route('GET /borrowedbooks/by-book/@book_id', function ($book_id) use ($borrowedBooksService) {
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::MEMBER]);
    Flight::json($borrowedBooksService->get_borrowed_by_book_id($book_id));
});

/**
 * @OA\Patch(
 *   path="/borrowedbooks/{id}/return",
 *   tags={"borrowedbooks"},
 *   summary="Mark borrowed record as returned",
 *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\Response(response=200, description="OK")
 * )
 */
Flight::route('PATCH /borrowedbooks/@id/return', function ($id) use ($booksService, $borrowedBooksService) {
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    try {
        $booksService->return_book(intval($id));
        $updated = $borrowedBooksService->get_by_id(intval($id));
        Flight::json(['success' => true, 'data' => $updated], 200);
    } catch (Throwable $e) {
        Flight::json(['success' => false, 'error' => $e->getMessage()], 400);
    }
});

?>
