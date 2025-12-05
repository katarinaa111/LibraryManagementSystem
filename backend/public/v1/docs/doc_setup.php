<?php
/**
 * @OA\Info(
 *     title="API",
 *     description="Library Management System API",
 *     version="1.0",
 *     @OA\Contact(
 *         email="sojakatarina@gmail.com",
 *         name="Katarina Šoja"
 *     )
 * )
 */
/**
 * @OA\Server(
 *     url= "http://localhost/KatarinaSoja/LibraryManagementSystem/backend",
 *     description="API server"
 * )
 */
/**
 * @OA\Components(
 *     @OA\SecurityScheme(
 *         securityScheme="ApiKey",
 *         type="apiKey",
 *         in="header",
 *         name="Authentication"
 *     )
 * )
 */
/**
 * @OA\OpenApi(
 *     security={{"ApiKey": {}}}
 * )
 */
