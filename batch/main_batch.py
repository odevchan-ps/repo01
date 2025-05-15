# batch/main_batch.py

import os
import sys
import argparse
import time
import logging

from fetch_news        import fetch_latest_news
from prompt_generation import generate_prompt, generate_post_text
from db_helper         import (
    connect_db,
    bulk_insert_news_articles_full,
    insert_news_article,
    fetch_unprocessed_articles,
    insert_prompt,
    mark_article_as_processed,
    insert_generated_post,
)
from datetime import datetime, timedelta
from logging.handlers import RotatingFileHandler
from datetime import datetime as _dt
import random
import os

def setup_logger():
    # ① 環境変数 LOG_DIR があればそこを、なければスクリプト隣の logs/
    default_dir = os.path.join(os.path.dirname(__file__), 'logs')
    log_dir = os.getenv('LOG_DIR', default_dir)
    os.makedirs(log_dir, exist_ok=True)

    # ② ルートロガーを使ってハンドラ重複を防止
    logger = logging.getLogger()
    logger.setLevel(logging.INFO)
    if not any(isinstance(h, RotatingFileHandler) for h in logger.handlers):
        log_path = os.path.join(log_dir, 'main_batch.log')
        # 10MB×5世代分でローテート
        rh = RotatingFileHandler(log_path, maxBytes=10*1024*1024, backupCount=5, encoding='utf-8')
        fmt = logging.Formatter('%(asctime)s [%(levelname)s] %(message)s')
        rh.setFormatter(fmt)
        logger.addHandler(rh)
    return logger

def main(auto_confirm=False):
    start_total = time.time()
    logger = setup_logger()

    print("=== main_batch 開始 ===\n")

    # ─── ステップ1: RSS取得 & 一括INSERT ────────────────────
    print("[ステップ1] 最新ニュースを取得中…")
    news_list = fetch_latest_news()
    total = len(news_list)
    print(f"[ステップ1] ニュース取得完了：{total} 件取得")

    # ─── ステップ1: 登録前の最新IDを取得 & ログ出力 ────────────────
    conn = connect_db()
    cursor = conn.cursor()
    cursor.execute("SELECT COUNT(*) FROM news_articles")
    before_count = cursor.fetchone()[0]
    cursor.close()
    conn.close()
    logger.info(f"[STEP1 Before] before_article_count={before_count}")

    # ─── ステップ1: 一括登録中… ────────────────────────
    print("[ステップ1] 一括登録中…")
    step1_start = time.time()

    # db_helper の bulk_insert_news_articles_full を呼び出し
    inserted = bulk_insert_news_articles_full(news_list)

    step1_duration = time.time() - step1_start

    # ─── ステップ1: 登録後の最新IDを取得 & ログ出力 ────────────────
    conn = connect_db()
    cursor = conn.cursor()

    cursor.execute("SELECT COUNT(*) FROM news_articles")
    after_count = cursor.fetchone()[0]

    cursor.close()
    conn.close()
    logger.info(f"[STEP1 After] latest_article_count={after_count}")

    inserted = after_count - before_count
    skipped  = total - inserted

    # 結果表示
    print(
        f"[ステップ1 結果] 登録成功={inserted}件, "
        f"スキップ={skipped}件 "
        f"(所要時間: {step1_duration:.2f}s)\n"
    )
    logger.info(f"[STEP1] 登録成功={inserted}件, スキップ={skipped}件")

    # 続行確認（--yes なら自動で y 扱い）
    if auto_confirm:
        answer = 'y'
    else:
        answer = input("ステップ2以降を実行しますか？ (y/n): ").strip().lower()
    if answer != 'y':
        print("処理を中断します。")
        sys.exit(0)

    # ─── ステップ2: プロンプト作成 & 投稿文生成 ─────────────
    # 環境変数から直近ウィンドウ(時間)と上限を取得（デフォルトは5時間以内, 3件）
    window_hours    = int(os.getenv("STEP2_WINDOW_HOURS", 5))
    max_articles    = int(os.getenv("STEP2_MAX_ARTICLES", 3))

    # 全未処理記事を取得し、published_at フィールドで絞り込み
    all_articles = fetch_unprocessed_articles()
    now = datetime.now()
    window_start = now - timedelta(hours=window_hours)

    # published_at フィールドを datetime に変換して、直近ウィンドウ内のみ抽出
    recent = []
    for a in all_articles:
        # published_at が datetime オブジェクトか文字列かを判定
        raw = a['published_at']
        if isinstance(raw, datetime):
            published = raw
        else:
            # 文字列の日付形式が YYYY-MM-DD HH:MM:SS か YYYY/MM/DD H:MM:SS の
            # どちらかを受け付ける
            try:
                published = datetime.strptime(raw, "%Y-%m-%d %H:%M:%S")
            except ValueError:
                published = datetime.strptime(raw, "%Y/%m/%d %H:%M:%S")
        # ウィンドウ内なら対象に追加
        if published >= window_start:
            recent.append(a)

    # 候補をランダムにシャッフル（または sample）して上限数だけ選択
    if len(recent) > max_articles:
        # random.sample で重複なくランダムに max_articles 件取得
        articles = random.sample(recent, max_articles)
    else:
        articles = recent

    total2 = len(articles)
    print(f"[ステップ2] 直近{window_hours}時間以内の記事からランダムに最大{max_articles}件を抽出：{total2}件 処理開始")

    step2_start = time.time()

    prompt_created = prompt_errors = post_generated = post_errors = 0

    for idx, article in enumerate(articles, start=1):
        # 疑似プログレス表示
        print(f"\r[ステップ2] {idx}/{total2} 件目 プロンプト・投稿文処理中...", end="", flush=True)

        aid = article['article_id']
        try:
            # プロンプト作成
            prompt_text = generate_prompt(article['summary'])
            pid = insert_prompt(article, prompt_text)
            mark_article_as_processed(pid, aid)
            prompt_created += 1
            logger.info(f"[ステップ2] プロンプト登録: 記事ID={aid} prompt_id={pid}")
        except Exception as e:
            prompt_errors += 1
            logger.error(f"[ステップ2] プロンプト作成エラー: 記事ID={aid} エラー={e}")
            continue

        # 投稿文生成
        try:
            post_text = generate_post_text(prompt_text, article['url'])
            if post_text:
                gen_id = insert_generated_post(pid, post_text)
                post_generated += 1
                logger.info(f"[ステップ2] 投稿文登録: 記事ID={aid} generated_id={gen_id}")
            else:
                logger.warning(f"[ステップ2] 投稿文未生成: 記事ID={aid}")
        except Exception as e:
            post_errors += 1
            logger.error(f"[ステップ2] 投稿文生成エラー: 記事ID={aid} エラー={e}")

    # ループ脱出後に改行
    print()
    step2_duration = time.time() - step2_start
    print(f"[ステップ2 結果] 記事数={total2}件, プロンプト作成={prompt_created}件, "
          f"作成エラー={prompt_errors}件, 投稿生成={post_generated}件, 投稿エラー={post_errors}件 "
          f"(所要時間: {step2_duration:.2f}s)\n")

    # ─── バッチ全体終了 ────────────────────────────────────
    total_duration = time.time() - start_total
    print(f"=== main_batch 終了 (総処理時間: {total_duration:.2f}s) ===")

if __name__ == "__main__":
    parser = argparse.ArgumentParser(description="NHK RSS→X自動投稿バッチ")
    parser.add_argument('-y','--yes', action='store_true',
                        help="ステップ2の確認プロンプトを自動で y 扱い（スキップ）")
    args = parser.parse_args()
    main(auto_confirm=args.yes)