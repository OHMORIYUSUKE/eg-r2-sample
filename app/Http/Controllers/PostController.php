<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePostRequest;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class PostController extends Controller
{
    #[OA\Get(
        path: "/v1/posts",
        summary: "Get all posts",
        description: "Retrieve a list of all posts",
        tags: ["posts"],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of posts",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(ref: "#/components/schemas/Post")
                )
            )
        ]
    )]
    public function index(): JsonResponse
    {
        $posts = Post::with('user')->get();
        return response()->json($posts);
    }

    #[OA\Post(
        path: "/v1/posts",
        summary: "Create a new post",
        description: "Create a new post with the provided data",
        tags: ["posts"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/CreatePostRequest")
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Post created successfully",
                content: new OA\JsonContent(ref: "#/components/schemas/Post")
            ),
            new OA\Response(
                response: 422,
                description: "Validation error"
            )
        ]
    )]
    public function store(CreatePostRequest $request): JsonResponse
    {
        $post = Post::create($request->validated());
        $post->load('user');
        return response()->json($post, 201);
    }

    #[OA\Get(
        path: "/v1/posts/{id}",
        summary: "Get a specific post",
        description: "Retrieve a specific post by ID",
        tags: ["posts"],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "Post ID",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer", format: "int64")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Post details",
                content: new OA\JsonContent(ref: "#/components/schemas/Post")
            ),
            new OA\Response(
                response: 404,
                description: "Post not found"
            )
        ]
    )]
    public function show(string $id): JsonResponse
    {
        $post = Post::with('user')->find($id);
        
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }
        
        return response()->json($post);
    }

    #[OA\Put(
        path: "/v1/posts/{id}",
        summary: "Update a post",
        description: "Update a specific post by ID",
        tags: ["posts"],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "Post ID",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer", format: "int64")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/CreatePostRequest")
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Post updated successfully",
                content: new OA\JsonContent(ref: "#/components/schemas/Post")
            ),
            new OA\Response(
                response: 404,
                description: "Post not found"
            ),
            new OA\Response(
                response: 422,
                description: "Validation error"
            )
        ]
    )]
    public function update(CreatePostRequest $request, string $id): JsonResponse
    {
        $post = Post::with('user')->find($id);
        
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }
        
        $post->update($request->validated());
        return response()->json($post);
    }

    #[OA\Delete(
        path: "/v1/posts/{id}",
        summary: "Delete a post",
        description: "Delete a specific post by ID",
        tags: ["posts"],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "Post ID",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer", format: "int64")
            )
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: "Post deleted successfully"
            ),
            new OA\Response(
                response: 404,
                description: "Post not found"
            )
        ]
    )]
    public function destroy(string $id): JsonResponse
    {
        $post = Post::find($id);
        
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }
        
        $post->delete();
        return response()->json(null, 204);
    }

    #[OA\Post(
        path: "/v1/users/{user}/posts",
        summary: "Create a post for a specific user",
        description: "Create a new post for a specific user",
        tags: ["posts", "users"],
        parameters: [
            new OA\Parameter(
                name: "user",
                description: "User ID",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer", format: "int64")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "title", type: "string", example: "My First Post"),
                    new OA\Property(property: "content", type: "string", example: "This is the content of my first post.")
                ],
                required: ["title", "content"]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Post created successfully for user",
                content: new OA\JsonContent(ref: "#/components/schemas/Post")
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
    public function createForUser(CreatePostRequest $request, string $user): JsonResponse
    {
        $userModel = User::find($user);
        
        if (!$userModel) {
            return response()->json(['message' => 'User not found'], 404);
        }
        
        $data = $request->validated();
        $data['user_id'] = $userModel->id;
        
        $post = Post::create($data);
        $post->load('user');
        
        return response()->json($post, 201);
    }
}