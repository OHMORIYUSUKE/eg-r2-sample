FROM php:8.2-fpm

# 作業ディレクトリを設定
WORKDIR /var/www

# 必要なシステムパッケージをインストール
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Composerをインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ユーザーを作成
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

# アプリケーションのファイルをコピー
COPY . /var/www

# 所有者を変更
RUN chown -R www:www /var/www

# ユーザーを切り替え
USER www

# ポート9000を公開
EXPOSE 9000

CMD ["php-fpm"]