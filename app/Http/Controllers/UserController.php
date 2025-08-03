<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class UserController extends Controller
{
    #[OA\Get(
        path: "/v1/users",
        summary: "Get all users",
        description: "Retrieve a list of all users",
        tags: ["users"],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of users",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(ref: "#/components/schemas/User")
                )
            )
        ]
    )]
    public function index(): JsonResponse
    {
        $users = User::with('posts')->get();
        return response()->json($users);
    }

    #[OA\Post(
        path: "/v1/users",
        summary: "Create a new user",
        description: "Create a new user with the provided data",
        tags: ["users"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/CreateUserRequest")
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "User created successfully",
                content: new OA\JsonContent(ref: "#/components/schemas/User")
            ),
            new OA\Response(
                response: 422,
                description: "Validation error",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "The given data was invalid."),
                        new OA\Property(
                            property: "errors",
                            type: "object",
                            example: ["email" => ["The email field is required."]]
                        )
                    ]
                )
            )
        ]
    )]
    public function store(CreateUserRequest $request): JsonResponse
    {
        $user = User::create($request->validated());
        return response()->json($user, 201);
    }

    #[OA\Get(
        path: "/v1/users/{id}",
        summary: "Get a specific user",
        description: "Retrieve a specific user by ID",
        tags: ["users"],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "User ID",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer", format: "int64")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "User details",
                content: new OA\JsonContent(ref: "#/components/schemas/User")
            ),
            new OA\Response(
                response: 404,
                description: "User not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "User not found")
                    ]
                )
            )
        ]
    )]
    public function show(string $id): JsonResponse
    {
        $user = User::with('posts')->find($id);
        
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        
        return response()->json($user);
    }

    #[OA\Put(
        path: "/v1/users/{id}",
        summary: "Update a user",
        description: "Update a specific user by ID",
        tags: ["users"],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "User ID",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer", format: "int64")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/CreateUserRequest")
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "User updated successfully",
                content: new OA\JsonContent(ref: "#/components/schemas/User")
            ),
            new OA\Response(
                response: 404,
                description: "User not found"
            ),
            new OA\Response(
                response: 422,
                description: "Validation error"
            )
        ]
    )]
    public function update(CreateUserRequest $request, string $id): JsonResponse
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        
        $user->update($request->validated());
        return response()->json($user);
    }

    #[OA\Delete(
        path: "/v1/users/{id}",
        summary: "Delete a user",
        description: "Delete a specific user by ID",
        tags: ["users"],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "User ID",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer", format: "int64")
            )
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: "User deleted successfully"
            ),
            new OA\Response(
                response: 404,
                description: "User not found"
            )
        ]
    )]
    public function destroy(string $id): JsonResponse
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        
        $user->delete();
        return response()->json(null, 204);
    }

    #[OA\Get(
        path: "/v1/users/{id}/posts",
        summary: "Get user's posts",
        description: "Retrieve all posts belonging to a specific user",
        tags: ["users", "posts"],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "User ID",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer", format: "int64")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of user's posts",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(ref: "#/components/schemas/Post")
                )
            ),
            new OA\Response(
                response: 404,
                description: "User not found"
            )
        ]
    )]
    public function posts(string $id): JsonResponse
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        
        return response()->json($user->posts);
    }
}