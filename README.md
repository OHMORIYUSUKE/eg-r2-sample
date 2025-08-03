# EG-R2 Laravel Sample Application

このプロジェクトは、スキーマ駆動開発をサポートする『[eg-r2](https://github.com/litalico-engineering/eg-r2)』ライブラリのサンプル実装です。

## 🚀 概要

- **フレームワーク**: Laravel 11.0
- **ライブラリ**: litalico-engineering/eg-r2, zircote/swagger-php  
- **開発環境**: Docker, Docker Compose
- **データベース**: MySQL 8.0
- **テスト**: PHPUnit (26個のE2Eテスト)
- **CI/CD**: GitHub Actions

## ✨ 特徴

- 📋 OpenAPI PHP Attributesによる仕様書作成
- 🔄 EG-R2によるスキーマ駆動開発の実践
- 🐳 Docker環境での1コマンド完全自動セットアップ
- 🚀 **自動生成されるAPIルート** (`routes/eg_r2.php`)
- 📚 Swagger UIによるAPIドキュメント表示
- 🧪 **26個の包括的なE2Eテスト**（日本語コメント付き）
- ⚙️ GitHub Actions による自動テスト実行

## 📁 プロジェクト構成

```
eg-r2-sample/
├── .github/workflows/        # GitHub Actions CI/CD
├── app/
│   ├── Http/
│   │   ├── Controllers/      # APIコントローラー（OpenAPI Attributes付き）
│   │   └── Requests/         # バリデーションリクエスト
│   └── Models/               # Eloquentモデル
├── config/
│   └── eg_r2.php            # EG-R2設定ファイル
├── database/
│   ├── factories/            # テストデータFactories
│   ├── migrations/           # データベースマイグレーション
│   └── seeders/              # サンプルデータ
├── docker/                   # Docker設定ファイル
├── routes/
│   ├── api.php              # 手動定義ルート
│   └── eg_r2.php            # 🚀 EG-R2自動生成ルート
├── tests/
│   └── Feature/Api/         # 26個のE2Eテスト
├── docker-start.sh          # 完全自動セットアップスクリプト
└── docker-compose.yml       # Docker環境定義
```

## 🚀 最短セットアップ（推奨）

```bash
git clone <this-repository>
cd eg-r2-sample
./docker-start.sh
```

**これだけで完了！** 初回起動でも以下を完全自動実行：
- ✅ .envファイル自動作成
- ✅ Docker環境構築・起動
- ✅ Composer依存関係インストール
- ✅ Laravel設定（キー生成等）
- ✅ データベース作成・マイグレーション
- ✅ サンプルデータ投入
- ✅ **EG-R2による自動ルート生成**

## 🌐 アクセス先

| 項目 | URL |
|------|-----|
| メインページ | http://localhost:8080 |
| Swagger UI | http://localhost:8080/api/documentation |
| OpenAPI JSON | http://localhost:8080/api/openapi.json |

## 📡 API エンドポイント

### ユーザー管理
- `GET /api/v1/users` - ユーザー一覧取得
- `POST /api/v1/users` - ユーザー作成
- `GET /api/v1/users/{id}` - ユーザー詳細取得  
- `PUT /api/v1/users/{id}` - ユーザー更新
- `DELETE /api/v1/users/{id}` - ユーザー削除
- `GET /api/v1/users/{id}/posts` - ユーザーの投稿一覧

### 投稿管理
- `GET /api/v1/posts` - 投稿一覧取得
- `POST /api/v1/posts` - 投稿作成
- `GET /api/v1/posts/{id}` - 投稿詳細取得
- `PUT /api/v1/posts/{id}` - 投稿更新
- `DELETE /api/v1/posts/{id}` - 投稿削除
- `POST /api/v1/users/{user}/posts` - 特定ユーザーの投稿作成

> 💡 **重要**: これらのルートは`routes/eg_r2.php`に**EG-R2によって自動生成**されます

## 🧪 テスト

### E2Eテスト実行

```bash
docker-compose exec app php artisan test
```

**26個のテスト** が全て日本語コメント付きで実装済み：
- ✅ UserAPI: 12テスト（CRUD + バリデーション + エラーハンドリング）
- ✅ PostAPI: 14テスト（CRUD + ユーザー関連 + 複雑ワークフロー）

### GitHub Actions CI/CD

PR作成時に自動テスト実行 + 結果コメント:

```yaml
# .github/workflows/docker-test.yml
# PRに自動でテスト結果がコメントされます
```

## ⚙️ EG-R2 Schema-Driven Development ワークフロー

### 1. OpenAPI定義（PHP Attributes）
```php
#[OA\Get(
    path: "/v1/users",
    summary: "Get all users",
    tags: ["users"]
)]
public function index(): JsonResponse
```

### 2. EG-R2による自動ルート生成
```bash
php artisan eg-r2:generate-route
# → routes/eg_r2.php に自動生成
```

### 3. Swagger UI確認
- http://localhost:8080/api/documentation

### 4. 実装・テスト
- コントローラーのビジネスロジック実装
- E2Eテストで検証

## 🔧 開発コマンド

### EG-R2関連
```bash
# ルート再生成
docker-compose exec app php artisan eg-r2:generate-route

# 設定確認
docker-compose exec app cat config/eg_r2.php
```

### データベース操作
```bash
# マイグレーション
docker-compose exec app php artisan migrate

# シーダー実行
docker-compose exec app php artisan db:seed

# ロールバック
docker-compose exec app php artisan migrate:rollback
```

### Docker操作
```bash
# 完全リセット
docker-compose down -v
./docker-start.sh

# ログ確認
docker-compose logs -f app
```

## 🛠️ 技術仕様

### PHP Dependencies
```json
{
  "laravel/framework": "^11.0",
  "litalico-engineering/eg-r2": "^1.0", 
  "zircote/swagger-php": "^4.0"
}
```

### EG-R2設定
```php
// config/eg_r2.php
return [
    'namespaces' => [
        'api' => 'App\Http\Controllers',
    ],
    'route_path' => base_path('routes/eg_r2.php'),
];
```

## 🔍 トラブルシューティング

### コンテナ関連
```bash
# 完全リビルド
docker-compose down -v
docker-compose up -d --build

# 権限修正
sudo chown -R $USER:$USER storage bootstrap/cache
```

### EG-R2関連
```bash
# ルートファイル確認
ls -la routes/eg_r2.php

# 設定確認
docker-compose exec app php artisan config:show eg_r2
```

## 📚 参考資料

- [EG-R2 GitHub](https://github.com/litalico-engineering/eg-r2)
- [Laravel 11 Documentation](https://laravel.com/docs/11.x)
- [OpenAPI 3.0 Specification](https://spec.openapis.org/oas/v3.0.3)
- [swagger-php Documentation](https://zircote.github.io/swagger-php/)

## 📄 ライセンス

MIT License

---
🧪 **GitHub Actions CI/CDテスト中** - この行は自動テスト確認用です