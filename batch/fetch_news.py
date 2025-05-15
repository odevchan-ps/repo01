# batch/fetch_news.py

import os
import json
import feedparser
from datetime import datetime
from zoneinfo import ZoneInfo
from email.utils import parsedate_to_datetime
from dotenv import load_dotenv
from pathlib import Path
from db_helper import connect_db, get_latest_published

load_dotenv()
# RSS フィードの URL リスト
rss_urls = {
    'society'      : "https://www3.nhk.or.jp/rss/news/cat1.xml",
    'politics'     : "https://www3.nhk.or.jp/rss/news/cat4.xml",
    'economy'      : "https://www3.nhk.or.jp/rss/news/cat5.xml",
    'entertainment': "https://www3.nhk.or.jp/rss/news/cat2.xml",
    'international': "https://www3.nhk.or.jp/rss/news/cat6.xml",
    'sports'       : "https://www3.nhk.or.jp/rss/news/cat7.xml",
    'science'      : "https://www3.nhk.or.jp/rss/news/cat3.xml",
}

# JSON からカテゴリーマッピングをロード
mapping_path = Path(__file__).parent / "category_mapping.json"
CATEGORY_MAP = json.loads(mapping_path.read_text(encoding="utf-8"))

def fetch_latest_news():
    # ─ STEP A: DB から最新登録日時を取得 ─────────────
    conn = connect_db()
    last_published = get_latest_published(conn)  # None or naive datetime
    conn.close()

    news_list = []

    # ─ STEP B: RSS 各カテゴリを巡回 ─────────────────
    for cat_name, url in rss_urls.items():
        code = CATEGORY_MAP.get(cat_name, "06")
        d = feedparser.parse(url)

        for entry in d.entries:
            # RSS が返す published (UTC) → tz-aware datetime
            dt_utc = parsedate_to_datetime(entry.published)
            dt_jst = dt_utc.astimezone(ZoneInfo("Asia/Tokyo"))

            # ── STEP C: naive にして比較 ──
            if last_published:
                # dt_jst は tz-aware → tz 情報を外して naive に
                dt_naive = dt_jst.replace(tzinfo=None)
                if dt_naive <= last_published:
                    # DBにある最新日時以前の記事はスキップ
                    continue

            # DB書き込み用フォーマット
            published_str = dt_jst.strftime('%Y-%m-%d %H:%M:%S')

            summary_text = entry.get('description', '')
            raw_id = entry.get('id', entry.get('link', ''))
            sid = raw_id.rsplit('/', 1)[-1][:50]

            news_list.append({
                'site_cd'             : '01',
                'title'               : entry.title,
                'url'                 : entry.link,
                'summary'             : summary_text,
                'published_at'        : published_str,
                'news_category_cd'    : code,
                'source_id'           : sid,
                'collection_method_cd': '01',
            })

    return news_list