# batch/fetch_news.py

import os
import json
import feedparser
from datetime import datetime
from zoneinfo import ZoneInfo
from email.utils import parsedate_to_datetime
from dotenv import load_dotenv
from pathlib import Path

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
    news_list = []
    for cat_name, url in rss_urls.items():
        # JSON から該当のコードを取得（デフォルトは“06”＝その他）
        news_category_cd = CATEGORY_MAP.get(cat_name, "06")

        d = feedparser.parse(url)
        for entry in d.entries:
            dt = parsedate_to_datetime(entry.published)
            jst_dt = dt.astimezone(ZoneInfo("Asia/Tokyo"))
            published = jst_dt.strftime('%Y-%m-%d %H:%M:%S')

            summary_text = entry.get('description', '')
            raw_id = entry.get('id', entry.get('link', ''))
            sid = raw_id.rsplit('/', 1)[-1][:50]

            news_list.append({
                'site_cd'             : '01',
                'title'               : entry.title,
                'url'                 : entry.link,
                'summary'             : summary_text,
                'published_at'        : published,
                'news_category_cd'    : news_category_cd,
                'source_id'           : sid,
                'collection_method_cd': '01',
            })
    return news_list
