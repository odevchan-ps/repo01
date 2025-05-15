FROM python:3.9-slim

# ① ビルドに必要なパッケージを先にインストール
RUN apt-get update && apt-get install -y \
      build-essential \
      python3-dev \
      libffi-dev \
      libssl-dev \
    && rm -rf /var/lib/apt/lists/*

# ② pip を最新版にしておくと wheel が取れる場合あり
RUN pip install --upgrade pip

WORKDIR /batch/linechat

COPY requirements.txt ./
# ③ ビルド依存が揃ってから pip install
RUN pip install --no-cache-dir -r requirements.txt

COPY . ./
RUN chmod +x entrypoint.sh

EXPOSE 5000
CMD ["./entrypoint.sh"]
