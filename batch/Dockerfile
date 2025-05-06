# syntax=docker/dockerfile:1.4
FROM python:3.11-slim

# 対話プロンプトを完全に抑制
ENV DEBIAN_FRONTEND=noninteractive

WORKDIR /app

# ① apt-utils を先に入れて、debconf 関連エラーをなくす
# ② アーカイブキャッシュのみマウント
RUN --mount=type=cache,id=apt-archives,target=/var/cache/apt/archives \
    apt-get update && \
    apt-get install -y --no-install-recommends \
      apt-utils \
      build-essential python3-dev libxml2-dev libxslt1-dev cron && \
    rm -rf /var/lib/apt/lists/*

# ③ requirements.txt を先にコピーして pip キャッシュマウント
COPY requirements.txt .
RUN --mount=type=cache,id=pip-cache,target=/root/.cache/pip \
    pip install --no-cache-dir -r requirements.txt

# ④ アプリ本体と crontab
COPY . .
COPY crontab /etc/cron.d/my-cron
RUN chmod 0644 /etc/cron.d/my-cron \
 && crontab /etc/cron.d/my-cron

CMD ["cron","-f"]
