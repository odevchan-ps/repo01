# db_helper.py

import os
import mysql.connector
from dotenv import load_dotenv
from datetime import datetime

# .env読み込み
load_dotenv()

def connect_db():
    return mysql.connector.connect(
        host=os.getenv("DB_HOST"),
        port=os.getenv("DB_PORT"),
        user=os.getenv("DB_USER"),
        password=os.getenv("DB_PASS"),
        database=os.getenv("DB_NAME"),
        charset="utf8mb4",
        use_unicode=True,
        init_command="SET NAMES utf8mb4"
    )

def insert_news_article(news) -> bool:
    """
    news_articles テーブルに記事を挿入します。
    既に同一 URL + カテゴリが存在する場合はスキップします。

    Returns:
        True  => 挿入成功
        False => 重複検知によりスキップ
    """
    conn = connect_db()
    cursor = conn.cursor(buffered=True)

    # ─── 重複チェック ───────────────────────────────
    sql_check = """
    SELECT prompt_id
      FROM news_articles
     WHERE url = %s
       AND news_category_cd = %s
     LIMIT 1
    """
    cursor.execute(sql_check, (news.get('url'), news.get('news_category_cd')))
    if cursor.fetchone():
        # 重複のためスキップ
        cursor.close()
        conn.close()
        return False
    # ────────────────────────────────────────────────

    # 新規挿入
    sql_insert = """
    INSERT INTO news_articles
      (site_cd, title, url, summary, published_at, news_category_cd,
       created_at, source_id, collection_method_cd)
    VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)
    """
    values = (
        news.get('site_cd'),
        news.get('title'),
        news.get('url'),
        news.get('summary'),
        news.get('published_at'),
        news.get('news_category_cd'),
        datetime.now(),
        news.get('source_id'),
        news.get('collection_method_cd')
    )

    cursor.execute(sql_insert, values)
    conn.commit()
    cursor.close()
    conn.close()
    return True

def get_latest_published(conn):
    with conn.cursor() as cur:
        cur.execute("SELECT MAX(published_at) FROM news_articles")
        return cur.fetchone()[0]

def bulk_insert_news_articles(conn, news_list):
    sql = """
      INSERT IGNORE INTO news_articles
        (title, url, summary, news_category_cd, published_at)
      VALUES (%s,%s,%s,%s,%s)
    """
    params = [
        (n['title'], n['url'], n['summary'],
         n['news_category_cd'], n['published_at'])
        for n in news_list
    ]
    with conn:
        with conn.cursor() as cur:
            cur.executemany(sql, params)
            return cur.rowcount


def bulk_insert_news_articles_full(news_list):
    """
    news_articles に複数レコードを一括 INSERT 。
    site_cd, title, url, summary, published_at,
    news_category_cd, created_at, source_id, collection_method_cd
    全フィールドを IGNORE 付きで投入します。
    """
    from datetime import datetime
    conn = connect_db()
    cursor = conn.cursor()

    sql = """
      INSERT IGNORE INTO news_articles
        (site_cd,
         title,
         url,
         summary,
         published_at,
         news_category_cd,
         created_at,
         source_id,
         collection_method_cd)
      VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)
    """

    now = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
    params = [
        (
            n['site_cd'],
            n['title'],
            n['url'],
            n['summary'],
            n['published_at'],
            n['news_category_cd'],
            now,
            n['source_id'],
            n['collection_method_cd'],
        )
        for n in news_list
    ]

    cursor.executemany(sql, params)
    conn.commit()
    cursor.close()
    conn.close()


def fetch_unprocessed_articles():
    conn = connect_db()
    cursor = conn.cursor(dictionary=True)

    sql = "SELECT * FROM news_articles WHERE prompt_id IS NULL"

    cursor.execute(sql)
    articles = cursor.fetchall()

    cursor.close()
    conn.close()

    return articles

def insert_x_post(article, summary):
    conn = connect_db()
    cursor = conn.cursor()

    sql = """
    INSERT INTO x_posts (x_post_id, user_id, created_at, text, processed)
    VALUES (%s, %s, %s, %s, %s)
    """

    # 仮のx_post_idを生成（例えば "news-{article_id}" みたいに）
    x_post_id = f"news-{article['article_id']}"
    user_id = "system_user"  # 仮ユーザーID
    created_at = datetime.now()
    text = summary
    processed = 0  # まだX投稿していないので未処理

    values = (x_post_id, user_id, created_at, text, processed)

    cursor.execute(sql, values)
    conn.commit()
    cursor.close()
    conn.close()

def mark_article_as_processed(new_prompt_id, article_id):
    conn = connect_db()
    cursor = conn.cursor()

    sql = "UPDATE news_articles SET prompt_id = %s WHERE article_id = %s"

    cursor.execute(sql, (new_prompt_id, article_id,))
    conn.commit()
    cursor.close()
    conn.close()

def insert_prompt(article, prompt_text):
    """
    x_prompts テーブルにプロンプトを INSERT し、その新規IDを返す
    """
    conn = connect_db()
    cursor = conn.cursor()
    sql = "INSERT INTO x_prompts (prompt_text, created_at) VALUES (%s, NOW())"
    cursor.execute(sql, (prompt_text,))
    new_id = cursor.lastrowid
    conn.commit()
    cursor.close()
    conn.close()
    return new_id

def insert_generated_post(prompt_id, generated_text):
    """
    x_generated_posts に生成ポスト文を INSERT し、その新規IDを返す
    """
    conn = connect_db()
    cursor = conn.cursor()
    sql = """
      INSERT INTO x_generated_posts
        (prompt_id, generated_text, created_at)
      VALUES (%s, %s, NOW())
    """
    cursor.execute(sql, (prompt_id, generated_text))
    new_id = cursor.lastrowid
    conn.commit()
    cursor.close()
    conn.close()
    return new_id
