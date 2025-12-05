<?php

require_once __DIR__ . '/../services/CategoriesService.php';
require_once __DIR__ . '/../data/Roles.php';

$categoriesService = new CategoriesService();

/**
 * @OA\Get(
 *   path="/categories",
 *   tags={"categories"},
 *   summary="List categories",
 *   @OA\Response(response=200, description="OK")
 * )
 */
Flight::route('GET /categories', function () use ($categoriesService) {
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::MEMBER]);
    Flight::json($categoriesService->get_all());
});

/**
 * @OA\Get(
 *   path="/categories/{id}",
 *   tags={"categories"},
 *   summary="Get category by ID",
 *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\Response(response=200, description="OK")
 * )
 */
Flight::route('GET /categories/@id', function ($id) use ($categoriesService) {
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::MEMBER]);
    Flight::json($categoriesService->get_by_id($id));
});

/**
 * @OA\Post(
 *   path="/categories",
 *   tags={"categories"},
 *   summary="Create category",
 *   @OA\RequestBody(required=true, content={
 *     @OA\MediaType(
 *       mediaType="application/json",
 *       @OA\Schema(
 *         type="object",
 *         required={"name"},
 *         @OA\Property(property="name", type="string")
 *       )
 *     )
 *   }),
 *   @OA\Response(response=200, description="Created")
 * )
 */
Flight::route('POST /categories', function () use ($categoriesService) {
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    $data = json_decode(Flight::request()->getBody(), true);
    try {
        Flight::json($categoriesService->add($data), 200);
    } catch (Throwable $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Put(
 *   path="/categories/{id}",
 *   tags={"categories"},
 *   summary="Update category",
 *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\RequestBody(required=true, content={
 *     @OA\MediaType(
 *       mediaType="application/json",
 *       @OA\Schema(
 *         type="object",
 *         required={"name"},
 *         @OA\Property(property="name", type="string")
 *       )
 *     )
 *   }),
 *   @OA\Response(response=200, description="OK")
 * )
 */
Flight::route('PUT /categories/@id', function ($id) use ($categoriesService) {
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    $data = json_decode(Flight::request()->getBody(), true);
    try {
        Flight::json($categoriesService->update($data, $id));
    } catch (Throwable $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Delete(
 *   path="/categories/{id}",
 *   tags={"categories"},
 *   summary="Delete category",
 *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\Response(response=200, description="OK")
 * )
 */
Flight::route('DELETE /categories/@id', function ($id) use ($categoriesService) {
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    Flight::json(['success' => $categoriesService->delete($id)]);
});

/**
 * @OA\Get(
 *   path="/categories/search",
 *   tags={"categories"},
 *   summary="Find category by name",
 *   @OA\Parameter(name="name", in="query", required=true, @OA\Schema(type="string")),
 *   @OA\Response(response=200, description="OK")
 * )
 */
Flight::route('GET /categories/search', function () use ($categoriesService) {
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::MEMBER]);
    $name = Flight::request()->query->name;
    Flight::json($categoriesService->get_category_by_name($name));
});

?>
