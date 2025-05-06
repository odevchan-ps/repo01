FROM python:3.11-slim

# 一度にまとめて必要パッケージを全部インストール
RUN apt-get update \
    && apt-get install -y \
         build-essential \
         python3-dev \
         libxml2-dev \
         libxslt1-dev \
         cron \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

# 依存だけコピーしてインストール
COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

# 残りすべてをコピー
COPY . .

# crontab をセット
COPY crontab /etc/cron.d/my-cron
RUN chmod 0644 /etc/cron.d/my-cron \
    && crontab /etc/cron.d/my-cron

CMD ["cron", "-f"]
