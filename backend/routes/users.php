<?php

require_once __DIR__ . '/../services/UsersService.php';
require_once __DIR__ . '/../data/Roles.php';

$usersService = new UsersService();

/**
 * @OA\Get(
 *   path="/users",
 *   tags={"users"},
 *   summary="List users",
 *   @OA\Response(response=200, description="OK")
 * )
 */
Flight::route('GET /users', function () use ($usersService) {
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::MEMBER]);
    Flight::json($usersService->get_all());
});

/**
 * @OA\Get(
 *   path="/users/{id}",
 *   tags={"users"},
 *   summary="Get user by ID",
 *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\Response(response=200, description="OK")
 * )
 */
Flight::route('GET /users/@id', function ($id) use ($usersService) {
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::MEMBER]);
    Flight::json($usersService->get_by_id($id));
});

/**
 * @OA\Post(
 *   path="/users",
 *   tags={"users"},
 *   summary="Create user",
 *   @OA\RequestBody(required=true, content={
 *     @OA\MediaType(
 *       mediaType="application/json",
 *       @OA\Schema(
 *         type="object",
 *         required={"username","email","role"},
 *         @OA\Property(property="username", type="string"),
 *         @OA\Property(property="email", type="string", format="email"),
 *         @OA\Property(property="role", type="string")
 *       )
 *     )
 *   }),
 *   @OA\Response(response=200, description="Created")
 * )
 */
Flight::route('POST /users', function () use ($usersService) {
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    $data = json_decode(Flight::request()->getBody(), true);
    try {
        Flight::json($usersService->add($data), 200);
    } catch (Throwable $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Put(
 *   path="/users/{id}",
 *   tags={"users"},
 *   summary="Update user",
 *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\RequestBody(required=true, content={
 *     @OA\MediaType(
 *       mediaType="application/json",
 *       @OA\Schema(
 *         type="object",
 *         required={"username","email","role"},
 *         @OA\Property(property="username", type="string"),
 *         @OA\Property(property="email", type="string", format="email"),
 *         @OA\Property(property="role", type="string")
 *       )
 *     )
 *   }),
 *   @OA\Response(response=200, description="OK")
 * )
 */
Flight::route('PUT /users/@id', function ($id) use ($usersService) {
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    $data = json_decode(Flight::request()->getBody(), true);
    try {
        Flight::json($usersService->update($data, $id));
    } catch (Throwable $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Delete(
 *   path="/users/{id}",
 *   tags={"users"},
 *   summary="Delete user",
 *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\Response(response=200, description="OK")
 * )
 */
Flight::route('DELETE /users/@id', function ($id) use ($usersService) {
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    Flight::json(['success' => $usersService->delete($id)]);
});

/**
 * @OA\Get(
 *   path="/users/by-email",
 *   tags={"users"},
 *   summary="Get user by email",
 *   @OA\Parameter(name="email", in="query", required=true, @OA\Schema(type="string", format="email")),
 *   @OA\Response(response=200, description="OK")
 * )
 */
Flight::route('GET /users/by-email', function () use ($usersService) {
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::MEMBER]);
    $email = Flight::request()->query->email;
    Flight::json($usersService->get_by_email($email));
});

/**
 * @OA\Get(
 *   path="/users/by-role/{role}",
 *   tags={"users"},
 *   summary="List users by role",
 *   @OA\Parameter(name="role", in="path", required=true, @OA\Schema(type="string")),
 *   @OA\Response(response=200, description="OK")
 * )
 */
Flight::route('GET /users/by-role/@role', function ($role) use ($usersService) {
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::MEMBER]);
    Flight::json($usersService->get_users_by_role($role));
});

?>
