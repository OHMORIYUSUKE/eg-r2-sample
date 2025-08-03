# 🚀 EG-R2 Sample - クイックスタートガイド

## 最短でアプリケーションを起動する

### 1. 必要な環境

- Docker
- Docker Compose

### 2. 完全自動 一発起動

```bash
./docker-start.sh
```

✨ **初回起動でも問題なし！** このスクリプトが以下を自動実行：

1. 📄 .envファイルの自動作成（存在しない場合）
2. 📦 Dockerコンテナの起動
3. ⏳ データベース起動の確認・待機
4. 🗄️ データベース作成の確認
5. 📦 Composerパッケージのインストール
6. 🔑 アプリケーションキーの生成
7. 🧹 キャッシュクリア
8. 🗄️ データベースマイグレーション
9. 📊 サンプルデータの投入

### 3. 完全リセット起動（トラブル時）

```bash
./setup-fresh.sh
```

このスクリプトは：
- 既存のコンテナ・ボリューム・.envを完全削除
- 初回起動状態に完全リセット
- その後、自動セットアップを実行

### 3. アクセス

- **メインページ**: http://localhost:8080
- **API Documentation**: http://localhost:8080/api/documentation
- **サンプルAPI**: http://localhost:8080/api/v1/users

### 4. 停止

```bash
docker-compose down
```

## 手動セットアップ

自動スクリプトを使わない場合は、以下のコマンドを順次実行：

```bash
# 1. コンテナ起動
docker-compose up -d

# 2. 依存関係インストール
docker-compose exec app composer install

# 3. アプリケーションキー生成
docker-compose exec app php artisan key:generate

# 4. マイグレーション実行
docker-compose exec app php artisan migrate

# 5. サンプルデータ投入
docker-compose exec app php artisan db:seed
```

## トラブルシューティング

### ポート8080が使用中の場合

`docker-compose.yml` の以下の行を変更：

```yaml
ports:
  - "8081:80"  # 8080 を 8081 に変更
```

### 権限エラーの場合

```bash
sudo chown -R $USER:$USER .
```

## API動作確認

### ユーザー一覧取得

```bash
curl http://localhost:8080/api/v1/users
```

### ユーザー作成

```bash
curl -X POST http://localhost:8080/api/v1/users \
  -H "Content-Type: application/json" \
  -d '{
    "name": "新規ユーザー",
    "email": "new@example.com"
  }'
```