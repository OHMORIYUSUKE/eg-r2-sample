<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "CreatePostRequest",
    type: "object",
    title: "Create Post Request",
    description: "Request schema for creating a post",
    required: ["title", "content"],
    properties: [
        new OA\Property(property: "title", type: "string", example: "My First Post"),
        new OA\Property(property: "content", type: "string", example: "This is the content of my first post."),
        new OA\Property(property: "user_id", type: "integer", format: "int64", description: "Author user ID (optional for user-specific endpoints)", example: 1),
    ]
)]
class CreatePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'user_id' => 'sometimes|required|integer|exists:users,id',
        ];
    }
}