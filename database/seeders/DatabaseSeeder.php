<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Post;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Check if users table exists
        if (!$this->tableExists('users')) {
            $this->command->info('Users table does not exist, skipping seeding.');
            return;
        }

        // Check if posts table exists  
        if (!$this->tableExists('posts')) {
            $this->command->info('Posts table does not exist, skipping seeding.');
            return;
        }

        // Create sample users
        $users = [
            [
                'name' => '田中 太郎',
                'email' => 'taro@example.com',
            ],
            [
                'name' => '佐藤 花子',
                'email' => 'hanako@example.com',
            ],
            [
                'name' => '山田 次郎',
                'email' => 'jiro@example.com',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']], // Search criteria
                $userData // Data to create if not found
            );
            
            // Create sample posts for each user (check if they don't exist)
            Post::firstOrCreate([
                'title' => $user->name . 'の最初の投稿',
                'user_id' => $user->id,
            ], [
                'content' => 'これは' . $user->name . 'の最初の投稿です。よろしくお願いします！',
            ]);
            
            Post::firstOrCreate([
                'title' => $user->name . 'の技術記事',
                'user_id' => $user->id,
            ], [
                'content' => 'Laravel と eg-r2 を使ったスキーマ駆動開発について書いてみました。',
            ]);
        }
    }

    /**
     * Check if a table exists in the database
     */
    private function tableExists(string $tableName): bool
    {
        try {
            return \Schema::hasTable($tableName);
        } catch (\Exception $e) {
            return false;
        }
    }
}