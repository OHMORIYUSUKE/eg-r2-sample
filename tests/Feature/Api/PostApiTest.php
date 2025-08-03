<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * テスト: GET /api/v1/posts - 全投稿取得
     */
    public function test_can_get_all_posts(): void
    {
        // 準備: ユーザーと投稿を作成
        $users = User::factory(2)->create();
        $posts = Post::factory(3)->create([
            'user_id' => $users->random()->id
        ]);

        // 実行: GETリクエストを送信
        $response = $this->getJson('/api/v1/posts');

        // 検証: レスポンスを確認
        $response->assertStatus(200)
                ->assertJsonCount(3)
                ->assertJsonStructure([
                    '*' => [
                        'id',
                        'title',
                        'content',
                        'user_id',
                        'created_at',
                        'updated_at',
                        'user' => [
                            'id',
                            'name',
                            'email',
                            'email_verified_at',
                            'created_at',
                            'updated_at'
                        ]
                    ]
                ]);
    }

    /**
     * テスト: POST /api/v1/posts - 新規投稿作成
     */
    public function test_can_create_post(): void
    {
        // 準備: ユーザーと投稿データを用意
        $user = User::factory()->create();
        $postData = [
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraph,
            'user_id' => $user->id,
        ];

        // 実行: POSTリクエストを送信
        $response = $this->postJson('/api/v1/posts', $postData);

        // 検証: レスポンスとデータベースを確認
        $response->assertStatus(201)
                ->assertJsonStructure([
                    'id',
                    'title',
                    'content',
                    'user_id',
                    'created_at',
                    'updated_at'
                ])
                ->assertJson([
                    'title' => $postData['title'],
                    'content' => $postData['content'],
                    'user_id' => $postData['user_id']
                ]);

        $this->assertDatabaseHas('posts', $postData);
    }

    /**
     * テスト: POST /api/v1/posts - バリデーションエラー
     */
    public function test_create_post_validation_errors(): void
    {
        // 実行: 無効なデータでPOSTリクエストを送信
        $response = $this->postJson('/api/v1/posts', []);

        // 検証: バリデーションエラーを確認
        $response->assertStatus(422)
                ->assertJsonValidationErrors(['title', 'content']);
    }

    /**
     * テスト: POST /api/v1/posts - 無効なuser_id
     */
    public function test_create_post_invalid_user_id(): void
    {
        // 実行: 存在しないユーザーでPOSTリクエストを送信
        $response = $this->postJson('/api/v1/posts', [
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraph,
            'user_id' => 999,
        ]);

        // 検証: バリデーションエラーを確認
        $response->assertStatus(422)
                ->assertJsonValidationErrors(['user_id']);
    }

    /**
     * テスト: GET /api/v1/posts/{id} - 特定投稿取得
     */
    public function test_can_get_specific_post(): void
    {
        // 準備: ユーザーと投稿を作成
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        // 実行: GETリクエストを送信
        $response = $this->getJson("/api/v1/posts/{$post->id}");

        // 検証: レスポンスを確認
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'id',
                    'title',
                    'content',
                    'user_id',
                    'created_at',
                    'updated_at',
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'email_verified_at',
                        'created_at',
                        'updated_at'
                    ]
                ])
                ->assertJson([
                    'id' => $post->id,
                    'title' => $post->title,
                    'content' => $post->content,
                    'user_id' => $user->id
                ]);
    }

    /**
     * テスト: GET /api/v1/posts/{id} - 投稿が見つからない場合
     */
    public function test_get_post_not_found(): void
    {
        // 実行: 存在しない投稿をリクエスト
        $response = $this->getJson('/api/v1/posts/999');

        // 検証: 404レスポンスを確認
        $response->assertStatus(404);
    }

    /**
     * テスト: PUT /api/v1/posts/{id} - 投稿更新
     */
    public function test_can_update_post(): void
    {
        // 準備: ユーザーと投稿を作成
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $updateData = [
            'title' => '更新されたタイトル',
            'content' => '更新された投稿内容です。',
        ];

        // 実行: PUTリクエストを送信
        $response = $this->putJson("/api/v1/posts/{$post->id}", $updateData);

        // 検証: レスポンスとデータベースを確認
        $response->assertStatus(200)
                ->assertJson([
                    'id' => $post->id,
                    'title' => $updateData['title'],
                    'content' => $updateData['content'],
                    'user_id' => $user->id
                ]);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => $updateData['title'],
            'content' => $updateData['content']
        ]);
    }

    /**
     * テスト: PUT /api/v1/posts/{id} - 更新バリデーションエラー
     */
    public function test_update_post_validation_errors(): void
    {
        // 準備: ユーザーと投稿を作成
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        // 実行: 無効なデータでPUTリクエストを送信
        $response = $this->putJson("/api/v1/posts/{$post->id}", [
            'title' => '',
            'content' => ''
        ]);

        // 検証: バリデーションエラーを確認
        $response->assertStatus(422)
                ->assertJsonValidationErrors(['title', 'content']);
    }

    /**
     * テスト: DELETE /api/v1/posts/{id} - 投稿削除
     */
    public function test_can_delete_post(): void
    {
        // 準備: ユーザーと投稿を作成
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        // 実行: DELETEリクエストを送信
        $response = $this->deleteJson("/api/v1/posts/{$post->id}");

        // 検証: レスポンスとデータベースを確認
        $response->assertStatus(204);
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    /**
     * テスト: DELETE /api/v1/posts/{id} - 存在しない投稿削除
     */
    public function test_delete_post_not_found(): void
    {
        // 実行: 存在しない投稿の削除を試行
        $response = $this->deleteJson('/api/v1/posts/999');

        // 検証: 404レスポンスを確認
        $response->assertStatus(404);
    }

    /**
     * テスト: POST /api/v1/users/{user}/posts - 特定ユーザーの投稿作成
     */
    public function test_can_create_post_for_user(): void
    {
        // 準備: ユーザーと投稿データを用意
        $user = User::factory()->create();
        $postData = [
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraph,
        ];

        // 実行: ユーザー専用エンドポイントにPOSTリクエストを送信
        $response = $this->postJson("/api/v1/users/{$user->id}/posts", $postData);

        // 検証: レスポンスとデータベースを確認
        $response->assertStatus(201)
                ->assertJsonStructure([
                    'id',
                    'title',
                    'content',
                    'user_id',
                    'created_at',
                    'updated_at'
                ])
                ->assertJson([
                    'title' => $postData['title'],
                    'content' => $postData['content'],
                    'user_id' => $user->id
                ]);

        $this->assertDatabaseHas('posts', [
            'title' => $postData['title'],
            'content' => $postData['content'],
            'user_id' => $user->id
        ]);
    }

    /**
     * テスト: POST /api/v1/users/{user}/posts - ユーザーが見つからない場合
     */
    public function test_create_post_for_user_not_found(): void
    {
        // 実行: 存在しないユーザーへの投稿作成を試行
        $response = $this->postJson('/api/v1/users/999/posts', [
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraph,
        ]);

        // 検証: 404レスポンスを確認
        $response->assertStatus(404);
    }

    /**
     * テスト: POST /api/v1/users/{user}/posts - バリデーションエラー
     */
    public function test_create_post_for_user_validation_errors(): void
    {
        // 準備: ユーザーを作成
        $user = User::factory()->create();

        // 実行: 無効なデータでPOSTリクエストを送信
        $response = $this->postJson("/api/v1/users/{$user->id}/posts", []);

        // 検証: バリデーションエラーを確認
        $response->assertStatus(422)
                ->assertJsonValidationErrors(['title', 'content']);
    }

    /**
     * テスト: 複雑なシナリオ - 複数の操作
     */
    public function test_complex_post_workflow(): void
    {
        // 1. ユーザー作成
        $userData = [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
        ];
        $userResponse = $this->postJson('/api/v1/users', $userData);
        $userResponse->assertStatus(201);
        $userId = $userResponse->json('id');

        // 2. ユーザーの投稿作成
        $postData = [
            'title' => 'テスト投稿',
            'content' => 'これはテスト投稿の内容です。',
        ];
        $postResponse = $this->postJson("/api/v1/users/{$userId}/posts", $postData);
        $postResponse->assertStatus(201);
        $postId = $postResponse->json('id');

        // 3. ユーザーの投稿一覧取得
        $userPostsResponse = $this->getJson("/api/v1/users/{$userId}/posts");
        $userPostsResponse->assertStatus(200)
                         ->assertJsonCount(1);

        // 4. 投稿更新
        $updateData = [
            'title' => '更新されたテスト投稿',
            'content' => '更新された内容です。',
        ];
        $updateResponse = $this->putJson("/api/v1/posts/{$postId}", $updateData);
        $updateResponse->assertStatus(200)
                      ->assertJson($updateData);

        // 5. 更新の確認
        $getResponse = $this->getJson("/api/v1/posts/{$postId}");
        $getResponse->assertStatus(200)
                   ->assertJson($updateData);

        // 6. 投稿削除
        $deleteResponse = $this->deleteJson("/api/v1/posts/{$postId}");
        $deleteResponse->assertStatus(204);

        // 7. 削除の確認
        $checkResponse = $this->getJson("/api/v1/posts/{$postId}");
        $checkResponse->assertStatus(404);
    }
}