# EG-R2 Laravel Sample Application

このプロジェクトは、スキーマ駆動開発をサポートする『[eg-r2](https://zenn.dev/litalico/articles/what-is-eg-r2)』ライブラリのサンプル実装です。

## 概要

- **フレームワーク**: Laravel 10.x
- **ライブラリ**: eg-r2, swagger-php
- **開発環境**: Docker, Docker Compose
- **データベース**: MySQL 8.0

## 特徴

- 📋 OpenAPI/Swaggerアノテーションによる仕様書作成
- 🔄 スキーマ駆動開発の実践
- 🐳 Docker環境での簡単セットアップ
- 🚀 自動生成されるバリデーションとルート
- 📚 Swagger UIによるAPIドキュメント表示

## プロジェクト構成

```
eg-r2-sample/
├── app/
│   ├── Http/
│   │   ├── Controllers/     # APIコントローラー（Swaggerアノテーション付き）
│   │   └── Requests/        # バリデーションリクエスト
│   ├── Models/              # Eloquentモデル
│   └── Providers/           # サービスプロバイダー
├── config/
│   └── eg-r2.php           # eg-r2の設定
├── database/
│   ├── migrations/          # データベースマイグレーション
│   └── seeders/             # サンプルデータ
├── docker/                  # Docker設定ファイル
├── routes/                  # ルート定義
└── resources/views/         # Swagger UIビュー
```

## セットアップ手順

### 🚀 最短セットアップ（推奨）

```bash
git clone <this-repository>
cd eg-r2-sample
./docker-start.sh
```

**これだけで完了！** 初回起動でも以下を自動実行：
- .envファイル自動作成
- Docker環境構築
- 依存関係インストール
- データベース設定
- サンプルデータ投入

### 🔧 手動セットアップ

自動セットアップスクリプトを使わない場合：

```bash
# 1. リポジトリのクローン
git clone <this-repository>
cd eg-r2-sample

# 2. Dockerコンテナの起動
docker-compose up -d

# 3. 依存関係のインストール
docker-compose exec app composer install

# 4. .envファイルの作成とキー生成
docker-compose exec app cp .env.example .env
docker-compose exec app php artisan key:generate

# 5. データベースマイグレーション
docker-compose exec app php artisan migrate

# 6. サンプルデータの投入
docker-compose exec app php artisan db:seed
```

## 利用方法

### アプリケーションの起動

```bash
docker-compose up -d
```

アプリケーションは [http://localhost:8080](http://localhost:8080) でアクセスできます。

### API仕様書の確認

Swagger UIは [http://localhost:8080/api/documentation](http://localhost:8080/api/documentation) でアクセスできます。

### 利用可能なエンドポイント

#### ユーザー管理

- `GET /api/v1/users` - ユーザー一覧取得
- `POST /api/v1/users` - ユーザー作成
- `GET /api/v1/users/{id}` - ユーザー詳細取得
- `PUT /api/v1/users/{id}` - ユーザー更新
- `DELETE /api/v1/users/{id}` - ユーザー削除
- `GET /api/v1/users/{id}/posts` - ユーザーの投稿一覧

#### 投稿管理

- `GET /api/v1/posts` - 投稿一覧取得
- `POST /api/v1/posts` - 投稿作成
- `GET /api/v1/posts/{id}` - 投稿詳細取得
- `PUT /api/v1/posts/{id}` - 投稿更新
- `DELETE /api/v1/posts/{id}` - 投稿削除
- `POST /api/v1/users/{user}/posts` - 特定ユーザーの投稿作成

### リクエスト例

#### ユーザー作成

```bash
curl -X POST http://localhost:8080/api/v1/users \\
  -H "Content-Type: application/json" \\
  -d '{
    "name": "テストユーザー",
    "email": "test@example.com"
  }'
```

#### 投稿作成

```bash
curl -X POST http://localhost:8080/api/v1/posts \\
  -H "Content-Type: application/json" \\
  -d '{
    "title": "サンプル投稿",
    "content": "これはサンプルの投稿内容です。",
    "user_id": 1
  }'
```

## 開発について

### OpenAPI仕様書の生成

Swaggerアノテーションから仕様書を生成:

```bash
docker-compose exec app php artisan vendor:publish --provider="L5Swagger\\L5SwaggerServiceProvider"
docker-compose exec app php artisan l5-swagger:generate
```

### データベース操作

#### マイグレーション

```bash
docker-compose exec app php artisan migrate
```

#### マイグレーションのロールバック

```bash
docker-compose exec app php artisan migrate:rollback
```

#### シーダーの実行

```bash
docker-compose exec app php artisan db:seed
```

### テストの実行

```bash
docker-compose exec app php artisan test
```

## スキーマ駆動開発のワークフロー

1. **API仕様設計**: コントローラーにSwaggerアノテーションを記述
2. **仕様書生成**: アノテーションからOpenAPI仕様書を自動生成
3. **バリデーション生成**: eg-r2がリクエストバリデーションを自動生成
4. **ルート生成**: 仕様に基づいてルートを自動生成
5. **実装**: コントローラーのビジネスロジックを実装
6. **テスト**: Swagger UIでAPIをテスト

## トラブルシューティング

### コンテナが起動しない場合

```bash
docker-compose down
docker-compose up -d --build
```

### 権限エラーが発生する場合

```bash
sudo chown -R $USER:$USER storage bootstrap/cache
```

### データベース接続エラー

`.env` ファイルのデータベース設定を確認してください:

```env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=secret
```

## EG-R2について

EG-R2は、スキーマ駆動開発を強力にサポートするライブラリです。

### 主な機能

- **自動バリデーション生成**: OpenAPI仕様からLaravelのバリデーションルールを自動生成
- **ルート自動生成**: 仕様書に基づいてルートファイルを自動生成
- **モックレスポンス**: 実装前でもAPIエンドポイントからレスポンスを返却
- **型安全性**: PHPDocとOpenAPIの型定義を連携

### 詳細情報

- [EG-R2 紹介記事](https://zenn.dev/litalico/articles/what-is-eg-r2)
- [スキーマ駆動開発フロー](https://zenn.dev/katzumi/articles/schema-driven-development-flow)

## ライセンス

このプロジェクトはMITライセンスの下で公開されています。

## 参考リンク

- [Laravel Documentation](https://laravel.com/docs)
- [OpenAPI Specification](https://spec.openapis.org/oas/v3.0.3)
- [swagger-php Documentation](https://zircote.github.io/swagger-php/)
- [Docker Documentation](https://docs.docker.com/)