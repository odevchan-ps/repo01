# syntax=docker/dockerfile:1.4
FROM python:3.11-slim
ENV DEBIAN_FRONTEND=noninteractive
ARG TARGETARCH
WORKDIR /app

# ──(A) apt で lxml のバイナリを先に入れる
RUN --mount=type=cache,id=apt-${TARGETARCH},target=/var/cache/apt/archives \
    apt-get update && \
    apt-get install -y --no-install-recommends \
      apt-utils \
      python3-lxml \
      build-essential python3-dev libxml2-dev libxslt1-dev cron && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# ──(B) requirements.txt から lxml 行を除いた新ファイルを生成
COPY requirements.txt .
RUN grep -v '^lxml' requirements.txt > req-no-lxml.txt

# ──(C) pip install は除外後のリストで
RUN --mount=type=cache,id=pip-${TARGETARCH},target=/root/.cache/pip \
    pip install --no-cache-dir -r req-no-lxml.txt

# ──(D) 残りファイルと crontab をコピー
COPY . .
COPY crontab /etc/cron.d/my-cron
RUN chmod 0644 /etc/cron.d/my-cron \
 && crontab /etc/cron.d/my-cron

CMD ["cron","-f"]
