# syntax=docker/dockerfile:1.4
FROM python:3.11-slim
ENV DEBIAN_FRONTEND=noninteractive
ARG TARGETARCH
WORKDIR /app

# --- 1) apt で lxml のバイナリを先に入れる --------------------------
RUN --mount=type=cache,id=apt-${TARGETARCH},target=/var/cache/apt/archives \
    apt-get update && \
    apt-get install -y --no-install-recommends \
      apt-utils \
      python3-lxml \
      build-essential python3-dev libxml2-dev libxslt1-dev cron && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# --- 2) pip では lxml をビルドさせない ------------------------------
COPY requirements.txt .
RUN --mount=type=cache,id=pip-${TARGETARCH},target=/root/.cache/pip \
    pip install --no-cache-dir --no-deps -r requirements.txt
    # --no-deps で requirements.txt に lxml が書かれていても無視できます
    # もし気になるなら requirements.txt から `lxml` 行を削除しても OK

# --- 3) 残りはそのまま ---------------------------------------------
COPY . .
COPY crontab /etc/cron.d/my-cron
RUN chmod 0644 /etc/cron.d/my-cron && crontab /etc/cron.d/my-cron
CMD ["cron", "-f"]
