#!/bin/bash

echo "🚀 =================================="
echo "🔥 EG-R2 Schema-Driven Development  "
echo "🔥 Laravel Sample Application       "
echo "🚀 =================================="
echo ""
echo "📚 eg-r2とは："
echo "   ✨ OpenAPIアトリビュートから自動ルート生成"
echo "   ✨ スキーマ駆動開発の実現"  
echo "   ✨ バリデーション & ルーティングの統合"
echo ""
echo "🐳 セットアップを開始します..."

# .envファイルの確認と作成
if [ ! -f .env ]; then
    echo "📄 .envファイルを作成中..."
    cp .env.example .env
    echo "✅ .env.exampleから.envをコピーしました"
else
    echo "✅ .envファイルは既に存在します"
fi

# Dockerコンテナの起動
echo "📦 Dockerコンテナを起動中..."
docker-compose up -d

# コンテナの起動完了を待つ
echo "⏳ コンテナの起動を待機中..."
sleep 10

# データベースの起動を確認
echo "🔍 データベース接続を確認中..."
until docker-compose exec db mysql -u root -psecret -e "SELECT 1" > /dev/null 2>&1; do
    echo "⏳ データベースの起動を待機中..."
    sleep 5
done
echo "✅ データベースが利用可能になりました"

# データベースの作成（念のため）
echo "🗄️ データベースを確認・作成中..."
docker-compose exec db mysql -u root -psecret -e "CREATE DATABASE IF NOT EXISTS laravel;" 2>/dev/null || true

# Composerパッケージのインストール
echo "📦 Composerパッケージをインストール中..."
docker-compose exec app composer install --no-interaction

# アプリケーションキーの生成
echo "🔑 アプリケーションキーを生成中..."
docker-compose exec app php artisan key:generate --force

# データベースマイグレーション
echo "🗄️ データベースマイグレーションを実行中..."
docker-compose exec app php artisan migrate --force 2>/dev/null || echo "ℹ️  マイグレーションをスキップしました"

# サンプルデータの投入
echo "📊 サンプルデータを投入中..."
docker-compose exec app php artisan db:seed --force

# EG-R2による自動ルート生成
echo ""
echo "⚙️  ===== EG-R2 自動ルート生成 ====="
echo "📋 OpenAPIアトリビュートからルートファイルを生成中..."
docker-compose exec app php artisan eg-r2:generate-route

echo "📋 自動生成されたルートファイル："
docker-compose exec app ls -la routes/eg_r2.php 2>/dev/null && echo "✅ routes/eg_r2.php が生成されました" || echo "⚠️  ルートファイルが見つかりません"

echo "🔍 生成されたAPIルート一覧："
docker-compose exec app php artisan route:list --path=api | grep -E "(users|posts)" | head -8 || echo "ルート情報を取得中..."

echo "✅ EG-R2による自動ルート生成完了"
echo "📝 従来の手動ルート定義が不要になりました"

echo ""
echo "🎉 ====================================="
echo "🎉 EG-R2 サンプル準備完了！            "
echo "🎉 ====================================="
echo ""
echo "🌟 今すぐ体験できること："
echo ""
echo "🌐 メインページ:"
echo "   http://localhost:8080"
echo ""
echo "📚 Swagger UI (自動生成されたAPI仕様書):"
echo "   http://localhost:8080/api/documentation"
echo ""
echo "⚡ 自動生成されたAPI (routes/eg_r2.php由来):"
echo "   GET  http://localhost:8080/api/v1/users    # ユーザー一覧"
echo "   POST http://localhost:8080/api/v1/users    # ユーザー作成"
echo "   GET  http://localhost:8080/api/v1/posts    # 投稿一覧"
echo "   POST http://localhost:8080/api/v1/posts    # 投稿作成"
echo ""
echo "🔧 EG-R2機能の確認方法:"
echo "   📁 cat routes/eg_r2.php                   # 自動生成ルート"
echo "   🔄 php artisan eg-r2:generate-route      # 再生成コマンド"
echo ""
echo "🛑 停止: docker-compose down"
echo ""
echo "📐 Schema-Driven Development による効率的な開発手法です。"