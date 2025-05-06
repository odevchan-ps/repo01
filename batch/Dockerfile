# syntax=docker/dockerfile:1.4
FROM python:3.11-slim

# apt アーカイブ領域のみキャッシュマウント
RUN --mount=type=cache,target=/var/cache/apt/archives \
    apt-get update && \
    apt-get install -y --no-install-recommends \
      build-essential python3-dev libxml2-dev libxslt1-dev cron && \
    rm -rf /var/lib/apt/lists/*

WORKDIR /app

# 依存定義を先にコピーしてキャッシュ化
COPY requirements.txt .
RUN --mount=type=cache,target=/root/.cache/pip \
    pip install --no-cache-dir -r requirements.txt

# アプリ本体と crontab をコピー
COPY . .
COPY crontab /etc/cron.d/my-cron
RUN chmod 0644 /etc/cron.d/my-cron \
    && crontab /etc/cron.d/my-cron

CMD ["cron", "-f"]