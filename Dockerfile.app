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

# Composer が必要ならインストール（省略可）
# RUN curl -sS https://getcomposer.org/installer | php \
#  && mv composer.phar /usr/local/bin/composer

# ソースをマウント or コピー（compose.yml の volumes に依存）

