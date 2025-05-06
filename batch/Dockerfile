# syntax=docker/dockerfile:1.4
FROM python:3.11-slim

# 1) 対話プロンプトを完全に抑制
ENV DEBIAN_FRONTEND=noninteractive

WORKDIR /app

# 2) apt 更新とインストールを分割＆dpkg オプションを追加
RUN --mount=type=cache,id=apt-archives,target=/var/cache/apt/archives \
    apt-get update && \
    DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends \
      apt-utils \
      build-essential python3-dev libxml2-dev libxslt1-dev cron \
      -o Dpkg::Options::="--force-confdef" \
      -o Dpkg::Options::="--force-confold" && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# 3) pip wheel のキャッシュマウント（lxml ビルド結果を永続化）
COPY requirements.txt .
RUN --mount=type=cache,id=pip-cache,target=/root/.cache/pip \
    pip install --no-cache-dir -r requirements.txt

# 4) アプリ本体と crontab
COPY . .
COPY crontab /etc/cron.d/my-cron
RUN chmod 0644 /etc/cron.d/my-cron \
 && crontab /etc/cron.d/my-cron

CMD ["cron","-f"]
