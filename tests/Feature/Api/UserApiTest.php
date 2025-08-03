<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * テスト: GET /api/v1/users - 全ユーザー取得
     */
    public function test_can_get_all_users(): void
    {
        // 準備: テストユーザーを作成
        $users = User::factory(3)->create();
        
        // 実行: GETリクエストを送信
        $response = $this->getJson('/api/v1/users');
        
        // 検証: レスポンスを確認
        $response->assertStatus(200)
                ->assertJsonCount(3)
                ->assertJsonStructure([
                    '*' => [
                        'id',
                        'name', 
                        'email',
                        'created_at',
                        'updated_at',
                        'posts'
                    ]
                ]);
    }

    /**
     * テスト: POST /api/v1/users - 新規ユーザー作成
     */
    public function test_can_create_user(): void
    {
        // 準備: ユーザーデータを用意
        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
        ];

        // 実行: POSTリクエストを送信
        $response = $this->postJson('/api/v1/users', $userData);

        // 検証: レスポンスとデータベースを確認
        $response->assertStatus(201)
                ->assertJsonStructure([
                    'id',
                    'name',
                    'email',
                    'created_at',
                    'updated_at'
                ])
                ->assertJson([
                    'name' => $userData['name'],
                    'email' => $userData['email']
                ]);

        $this->assertDatabaseHas('users', $userData);
    }

    /**
     * テスト: POST /api/v1/users - バリデーションエラー
     */
    public function test_create_user_validation_errors(): void
    {
        // 実行: 無効なデータでPOSTリクエストを送信
        $response = $this->postJson('/api/v1/users', []);

        // 検証: バリデーションエラーを確認
        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'email']);
    }

    /**
     * テスト: POST /api/v1/users - メールアドレス重複チェック
     */
    public function test_create_user_email_must_be_unique(): void
    {
        // 準備: 既存ユーザーを作成
        $existingUser = User::factory()->create();

        // 実行: 同じメールアドレスでユーザー作成を試行
        $response = $this->postJson('/api/v1/users', [
            'name' => $this->faker->name,
            'email' => $existingUser->email,
        ]);

        // 検証: バリデーションエラーを確認
        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
    }

    /**
     * テスト: GET /api/v1/users/{id} - 特定ユーザー取得
     */
    public function test_can_get_specific_user(): void
    {
        // 準備: 投稿付きユーザーを作成
        $user = User::factory()->create();
        $posts = Post::factory(2)->create(['user_id' => $user->id]);

        // 実行: GETリクエストを送信
        $response = $this->getJson("/api/v1/users/{$user->id}");

        // 検証: レスポンスを確認
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'id',
                    'name',
                    'email', 
                    'created_at',
                    'updated_at',
                    'posts' => [
                        '*' => [
                            'id',
                            'title',
                            'content',
                            'user_id',
                            'created_at',
                            'updated_at'
                        ]
                    ]
                ])
                ->assertJson([
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email
                ]);

        // 投稿が含まれていることを確認
        $this->assertCount(2, $response->json('posts'));
    }

    /**
     * テスト: GET /api/v1/users/{id} - ユーザーが見つからない場合
     */
    public function test_get_user_not_found(): void
    {
        // 実行: 存在しないユーザーをリクエスト
        $response = $this->getJson('/api/v1/users/999');

        // 検証: 404レスポンスを確認
        $response->assertStatus(404);
    }

    /**
     * テスト: PUT /api/v1/users/{id} - ユーザー更新
     */
    public function test_can_update_user(): void
    {
        // 準備: ユーザーを作成
        $user = User::factory()->create();
        $updateData = [
            'name' => '更新された名前',
            'email' => 'updated@example.com',
        ];

        // 実行: PUTリクエストを送信
        $response = $this->putJson("/api/v1/users/{$user->id}", $updateData);

        // 検証: レスポンスとデータベースを確認
        $response->assertStatus(200)
                ->assertJson([
                    'id' => $user->id,
                    'name' => $updateData['name'],
                    'email' => $updateData['email']
                ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => $updateData['name'],
            'email' => $updateData['email']
        ]);
    }

    /**
     * テスト: PUT /api/v1/users/{id} - 更新バリデーションエラー
     */
    public function test_update_user_validation_errors(): void
    {
        // 準備: ユーザーを作成
        $user = User::factory()->create();

        // 実行: 無効なデータでPUTリクエストを送信
        $response = $this->putJson("/api/v1/users/{$user->id}", [
            'name' => '',
            'email' => 'invalid-email'
        ]);

        // 検証: バリデーションエラーを確認
        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'email']);
    }

    /**
     * テスト: DELETE /api/v1/users/{id} - ユーザー削除
     */
    public function test_can_delete_user(): void
    {
        // 準備: ユーザーを作成
        $user = User::factory()->create();

        // 実行: DELETEリクエストを送信
        $response = $this->deleteJson("/api/v1/users/{$user->id}");

        // 検証: レスポンスとデータベースを確認
        $response->assertStatus(204);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    /**
     * テスト: DELETE /api/v1/users/{id} - 存在しないユーザー削除
     */
    public function test_delete_user_not_found(): void
    {
        // 実行: 存在しないユーザーの削除を試行
        $response = $this->deleteJson('/api/v1/users/999');

        // 検証: 404レスポンスを確認
        $response->assertStatus(404);
    }

    /**
     * テスト: GET /api/v1/users/{id}/posts - ユーザーの投稿取得
     */
    public function test_can_get_user_posts(): void
    {
        // 準備: 投稿付きユーザーを作成
        $user = User::factory()->create();
        $posts = Post::factory(3)->create(['user_id' => $user->id]);

        // 実行: GETリクエストを送信
        $response = $this->getJson("/api/v1/users/{$user->id}/posts");

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
                        'updated_at'
                    ]
                ]);

        // 全ての投稿がそのユーザーのものであることを確認
        foreach ($response->json() as $post) {
            $this->assertEquals($user->id, $post['user_id']);
        }
    }

    /**
     * テスト: GET /api/v1/users/{id}/posts - ユーザーが見つからない場合
     */
    public function test_get_user_posts_user_not_found(): void
    {
        // 実行: 存在しないユーザーの投稿をリクエスト
        $response = $this->getJson('/api/v1/users/999/posts');

        // 検証: 404レスポンスを確認
        $response->assertStatus(404);
    }
}