<?php

require_once __DIR__ . '/../services/AuthorsService.php';
require_once __DIR__ . '/../data/Roles.php';

$authorsService = new AuthorsService();

/**
 * @OA\Get(
 *   path="/authors",
 *   tags={"authors"},
 *   summary="List authors",
 *   @OA\Response(response=200, description="OK")
 * )
 */
Flight::route('GET /authors', function () use ($authorsService) {
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::MEMBER]);
    Flight::json($authorsService->get_all());
});

/**
 * @OA\Get(
 *   path="/authors/{id}",
 *   tags={"authors"},
 *   summary="Get author by ID",
 *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\Response(response=200, description="OK")
 * )
 */
Flight::route('GET /authors/@id', function ($id) use ($authorsService) {
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::MEMBER]);
    Flight::json($authorsService->get_by_id($id));
});

/**
 * @OA\Post(
 *   path="/authors",
 *   tags={"authors"},
 *   summary="Create author",
 *   @OA\RequestBody(required=true, content={
 *     @OA\MediaType(
 *       mediaType="application/json",
 *       @OA\Schema(
 *         type="object",
 *         required={"name"},
 *         @OA\Property(property="name", type="string"),
 *         @OA\Property(property="bio", type="string")
 *       )
 *     )
 *   }),
 *   @OA\Response(response=200, description="Created")
 * )
 */
Flight::route('POST /authors', function () use ($authorsService) {
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    $data = json_decode(Flight::request()->getBody(), true);
    try {
        Flight::json($authorsService->add($data), 200);
    } catch (Throwable $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Put(
 *   path="/authors/{id}",
 *   tags={"authors"},
 *   summary="Update author",
 *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\RequestBody(required=true, content={
 *     @OA\MediaType(
 *       mediaType="application/json",
 *       @OA\Schema(
 *         type="object",
 *         required={"name"},
 *         @OA\Property(property="name", type="string"),
 *         @OA\Property(property="bio", type="string")
 *       )
 *     )
 *   }),
 *   @OA\Response(response=200, description="OK")
 * )
 */
Flight::route('PUT /authors/@id', function ($id) use ($authorsService) {
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    $data = json_decode(Flight::request()->getBody(), true);
    try {
        Flight::json($authorsService->update($data, $id));
    } catch (Throwable $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Delete(
 *   path="/authors/{id}",
 *   tags={"authors"},
 *   summary="Delete author",
 *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\Response(response=200, description="OK")
 * )
 */
Flight::route('DELETE /authors/@id', function ($id) use ($authorsService) {
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    Flight::json(['success' => $authorsService->delete($id)]);
});

/**
 * @OA\Get(
 *   path="/authors/search",
 *   tags={"authors"},
 *   summary="Find author by name",
 *   @OA\Parameter(name="name", in="query", required=true, @OA\Schema(type="string")),
 *   @OA\Response(response=200, description="OK")
 * )
 */
Flight::route('GET /authors/search', function () use ($authorsService) {
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::MEMBER]);
    $name = Flight::request()->query->getData()['name'];
    Flight::json($authorsService->get_author_by_name($name));
});

?>