# batch/main_batch.py

import os
import sys
import time
import logging

from fetch_news        import fetch_latest_news
from prompt_generation import generate_prompt, generate_post_text
from db_helper         import (
    insert_news_article,
    fetch_unprocessed_articles,
    insert_prompt,
    mark_article_as_processed,
    insert_generated_post,
)

def setup_logger():
    script_dir = os.path.dirname(os.path.abspath(__file__))
    log_dir    = os.path.join(script_dir, 'logs')
    os.makedirs(log_dir, exist_ok=True)

    logger = logging.getLogger('main_batch')
    logger.setLevel(logging.INFO)
    fh = logging.FileHandler(os.path.join(log_dir, 'main_batch.log'))
    fh.setLevel(logging.INFO)
    fh.setFormatter(logging.Formatter('%(asctime)s [%(levelname)s] %(message)s'))
    logger.addHandler(fh)
    return logger

def main():
    start_total = time.time()
    logger = setup_logger()

    print("=== main_batch 開始 ===\n")

    # ─── ステップ1: RSS取得 & DB登録 ────────────────────────
    print("[ステップ1] 最新ニュースを取得中...")
    news_list = fetch_latest_news()
    fetched = len(news_list)
    print(f"[ステップ1] ニュース取得完了：{fetched} 件取得")

    print("[ステップ1] ニュース登録中...")
    step1_start = time.time()
    inserted = skipped = errors1 = 0

    for idx, news in enumerate(news_list, start=1):
        # 疑似プログレス表示
        print(f"\r[ステップ1] {idx}/{fetched} 件目 処理中...", end="", flush=True)

        try:
            ok = insert_news_article(news)
            if ok:
                inserted += 1
                logger.info(f"[ステップ1] 登録成功: URL={news['url']}")
            else:
                skipped += 1
                logger.info(f"[ステップ1] スキップ(重複): URL={news['url']}")
        except Exception as e:
            errors1 += 1
            logger.error(f"[ステップ1] 登録エラー: URL={news.get('url')} エラー={e}")

    # ループ脱出後に改行
    print()
    step1_duration = time.time() - step1_start
    print(f"[ステップ1 結果] 登録成功={inserted}件, スキップ={skipped}件, エラー={errors1}件 "
          f"(所要時間: {step1_duration:.2f}s)\n")

    # 続行確認
    answer = input("ステップ2以降を実行しますか？ (y/n): ").strip().lower()
    if answer != 'y':
        print("処理を中断します。")
        sys.exit(0)

    # ─── ステップ2: プロンプト作成 & 投稿文生成 ─────────────
    articles = fetch_unprocessed_articles()
    total2 = len(articles)
    print(f"[ステップ2] 未処理記事取得：{total2}件 処理開始")
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
    main()
