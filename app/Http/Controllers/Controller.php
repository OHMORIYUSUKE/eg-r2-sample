<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    description: "eg-r2 Laravel Sample API",
    title: "EG-R2 Sample API",
)]
#[OA\Server(
    url: "http://localhost:8080/api",
    description: "Local development server"
)]
#[OA\Tag(
    name: "users",
    description: "User management"
)]
#[OA\Tag(
    name: "posts",
    description: "Post management"
)]
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}