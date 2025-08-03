<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EG-R2 Sample Application</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
        }
        .links {
            text-align: center;
            margin-top: 30px;
        }
        .links a {
            display: inline-block;
            margin: 10px;
            padding: 12px 24px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .links a:hover {
            background-color: #0056b3;
        }
        .description {
            margin: 20px 0;
            line-height: 1.6;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>EG-R2 Sample Application</h1>
        
        <div class="description">
            <p>このアプリケーションは、スキーマ駆動開発をサポートする『eg-r2』ライブラリのサンプル実装です。</p>
            <p>Laravel フレームワークとswagger-phpアノテーションを使用して、OpenAPI仕様書からAPIを自動生成しています。</p>
        </div>

        <div class="links">
            <a href="/api/documentation">API Documentation</a>
            <a href="/api/v1/users">Users API</a>
            <a href="/api/v1/posts">Posts API</a>
        </div>

        <div class="description">
            <h3>利用可能なエンドポイント:</h3>
            <ul>
                <li><strong>GET</strong> /api/v1/users - ユーザー一覧取得</li>
                <li><strong>POST</strong> /api/v1/users - ユーザー作成</li>
                <li><strong>GET</strong> /api/v1/users/{id} - ユーザー詳細取得</li>
                <li><strong>PUT</strong> /api/v1/users/{id} - ユーザー更新</li>
                <li><strong>DELETE</strong> /api/v1/users/{id} - ユーザー削除</li>
                <li><strong>GET</strong> /api/v1/users/{id}/posts - ユーザーの投稿一覧</li>
                <li><strong>GET</strong> /api/v1/posts - 投稿一覧取得</li>
                <li><strong>POST</strong> /api/v1/posts - 投稿作成</li>
                <li><strong>GET</strong> /api/v1/posts/{id} - 投稿詳細取得</li>
                <li><strong>PUT</strong> /api/v1/posts/{id} - 投稿更新</li>
                <li><strong>DELETE</strong> /api/v1/posts/{id} - 投稿削除</li>
                <li><strong>POST</strong> /api/v1/users/{user}/posts - 特定ユーザーの投稿作成</li>
            </ul>
        </div>
    </div>
</body>
</html>