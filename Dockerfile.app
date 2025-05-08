# Dockerfile.app
FROM php:8.2-apache

# 必要パッケージと pdo_mysql をインストール
RUN apt-get update \
 && apt-get install -y libzip-dev zip libonig-dev \
 && docker-php-ext-install pdo_mysql \
 && a2enmod rewrite \
 && apt-get clean \
 && rm -rf /var/lib/apt/lists/*

# ドキュメントルート設定は .env か compose に任せても OK
WORKDIR /var/www/html

# ── Composer CLI のインストール ──────────────────────────────────────
RUN apt-get update \
 && apt-get install -y --no-install-recommends curl unzip \
 && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
 && rm -rf /var/lib/apt/lists/*

# ── Composer 依存をバイナリ化 ───────────────────────────────────────
# ※composer.json / composer.lock がある場合のみ
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist \
 && rm -rf ~/.composer

# Composer が必要ならインストール（省略可）
# RUN curl -sS https://getcomposer.org/installer | php \
#  && mv composer.phar /usr/local/bin/composer

# ソースをマウント or コピー（compose.yml の volumes に依存）

