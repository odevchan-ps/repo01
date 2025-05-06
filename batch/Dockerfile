# syntax=docker/dockerfile:1.4
FROM python:3.11-slim

# 1) 非対話モードで dpkg を動かす
ENV DEBIAN_FRONTEND=noninteractive

# 2) apt キャッシュ（.deb アーカイブ）だけマウントして高速化
RUN --mount=type=cache,target=/var/cache/apt/archives \
    apt-get update && \
    DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends \
      build-essential python3-dev libxml2-dev libxslt1-dev cron && \
    rm -rf /var/lib/apt/lists/*

WORKDIR /app

# 3) pip キャッシュマウントで wheel ビルド結果を保存
COPY requirements.txt .
RUN --mount=type=cache,target=/root/.cache/pip \
    pip install --no-cache-dir -r requirements.txt

# 4) アプリ本体と crontab セットアップ
COPY . .
COPY crontab /etc/cron.d/my-cron
RUN chmod 0644 /etc/cron.d/my-cron \
 && crontab /etc/cron.d/my-cron

CMD ["cron","-f"]
