import json
import feedparser
from datetime import datetime, timedelta
import pytz

# NHK ニュース RSS フィードの URL リスト
rss_urls = {
    'general': "https://www3.nhk.or.jp/rss/news/cat0.xml",  # 全般
    'society': "https://www3.nhk.or.jp/rss/news/cat1.xml",  # 社会
    'politics': "https://www3.nhk.or.jp/rss/news/cat4.xml",  # 政治
    'economy': "https://www3.nhk.or.jp/rss/news/cat5.xml",  # 経済
    'entertainment': "https://www3.nhk.or.jp/rss/news/cat2.xml",  # エンタメ
    'international': "https://www3.nhk.or.jp/rss/news/cat6.xml",  # 国際
    'sports': "https://www3.nhk.or.jp/rss/news/cat7.xml",  # スポーツ
    'science': "https://www3.nhk.or.jp/rss/news/cat3.xml",  # 科学
}

# RSS フィードとニュースカテゴリーのマッピング
category_mapping = {
    'general': '01',        # 全般
    'society': '02',        # 政治・経済・社会
    'politics': '02',       # 政治・経済・社会
    'economy': '02',        # 政治・経済・社会
    'entertainment': '03',  # エンタメ
    'international': '06',  # その他
    'sports': '04',         # スポーツ
    'science': '05',        # IT・科学
}

def fetch_feed(url):
    return feedparser.parse(url)

def process_entries(entries, category_code):
    tz = pytz.timezone('Asia/Tokyo')  # 日本標準時
    now = datetime.now(tz)
    today = now.strftime('%Y-%m-%d')
    yesterday = (now - timedelta(days=1)).strftime('%Y-%m-%d')
    
    articles = []

    for entry in entries:
        article_date = entry.published_parsed
        article_datetime = datetime(*article_date[:6]).replace(tzinfo=pytz.utc).astimezone(tz)
        article_date_str = article_datetime.strftime('%Y-%m-%d %H:%M:%S')
        
        if article_date_str.startswith(today) or article_date_str.startswith(yesterday):
            article_url = entry.link
            article_title = entry.title
            article_summary = entry.summary

            # 最後の句点以降を切り捨てる
            last_period_index = article_summary.rfind('。')
            if last_period_index != -1:
                article_summary = article_summary[:last_period_index + 1]

            articles.append({
                'url': article_url,
                'title': article_title,
                'summary': article_summary,
                'published_at': article_date_str,
                'news_category_cd': category_code
            })
    return articles

def main():
    all_articles = []

    for category, url in rss_urls.items():
        feed = fetch_feed(url)
        category_code = category_mapping.get(category, '01')  # デフォルトは '01'
        articles = process_entries(feed.entries, category_code)
        all_articles.extend(articles)
    
    # JSON形式で出力
    print(json.dumps(all_articles, ensure_ascii=False))

if __name__ == '__main__':
    main()
