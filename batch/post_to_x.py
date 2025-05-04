import sys
import json
import os
from requests_oauthlib import OAuth1Session

# 現在のファイルのディレクトリを取得
current_directory = os.path.dirname(os.path.abspath(__file__))

# APIキーを同じフォルダ内のファイルから読み込む
api_key_path = os.path.join(current_directory, 'api_key.txt')

def load_api_keys(api_key_path):
    keys = {}
    with open(api_key_path, 'r') as file:
        for line in file:
            name, value = line.strip().split('=')
            keys[name] = value
    return keys

api_keys = load_api_keys(api_key_path)

# Twitter APIのキーとトークンを設定
consumer_key = api_keys['x_consumer_key']
consumer_secret = api_keys['x_consumer_secret']
access_token = api_keys['x_access_token']
access_token_secret = api_keys['x_access_token_secret']

def post_to_x(message):
    url = "https://api.twitter.com/2/tweets"
    
    # OAuth 1.0a セッションを設定
    oauth = OAuth1Session(
        consumer_key,
        client_secret=consumer_secret,
        resource_owner_key=access_token,
        resource_owner_secret=access_token_secret
    )

    payload = {
        "text": message
    }

    response = oauth.post(url, json=payload)

    try:
        response_json = response.json()
    except ValueError:
        return {"error": "Invalid JSON response from Twitter API"}

    if response.status_code == 201:
        tweet_id = response_json.get("data", {}).get("id")
        text = message
        
        return {
            "x_post_id": tweet_id,
            "text": text
        }
    else:
        return {"error": response_json}

if __name__ == "__main__":
    if len(sys.argv) > 1:
        message = sys.argv[1]
        result = post_to_x(message)
        print(json.dumps(result))
    else:
        print(json.dumps({"error": "No message provided"}))
