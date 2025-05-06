# syntax=docker/dockerfile:1.4

FROM python:3.11-slim
ENV DEBIAN_FRONTEND=noninteractive

ARG TARGETARCH            # ★ 追記：BuildKit が自動で渡す
WORKDIR /app

# アーカイブ用キャッシュをアーキ別に
RUN --mount=type=cache,id=apt-${TARGETARCH},target=/var/cache/apt/archives \
    apt-get update && \
    apt-get install -y --no-install-recommends \
      apt-utils \
      build-essential python3-dev libxml2-dev libxslt1-dev cron \
      -o Dpkg::Options::="--force-confdef" \
      -o Dpkg::Options::="--force-confold" && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# 3) pip wheel キャッシュもアーキ別に
COPY requirements.txt .
RUN --mount=type=cache,id=pip-${TARGETARCH},target=/root/.cache/pip \
    pip install --no-cache-dir -r requirements.txt

# 4) アプリ本体と crontab
COPY . .
COPY crontab /etc/cron.d/my-cron
RUN chmod 0644 /etc/cron.d/my-cron \
 && crontab /etc/cron.d/my-cron

CMD ["cron","-f"]
