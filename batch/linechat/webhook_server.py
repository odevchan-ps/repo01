import os
from flask import Flask, request, abort
from linebot import LineBotApi, WebhookHandler
from linebot.exceptions import InvalidSignatureError
from linebot.models import MessageEvent, TextMessage, TextSendMessage
from openai import OpenAI
import config

# 環境変数から OpenAI API Key を設定
os.environ["OPENAI_API_KEY"] = os.getenv("OPENAI_API_KEY")
client = OpenAI()

app = Flask(__name__)
handler = WebhookHandler(config.LINE_SECRET)
line_api = LineBotApi(config.LINE_TOKEN)

@app.route("/callback", methods=["POST"])
def callback():
    signature = request.headers.get("X-Line-Signature", "")
    body = request.get_data(as_text=True)
    try:
        handler.handle(body, signature)
    except InvalidSignatureError:
        abort(400)
    return "OK"

@handler.add(MessageEvent, message=TextMessage)
def handle_message(event):
    # 1:1 チャットなら userId をログに出力
    if event.source.type == "user":
        print("=== User ID ===")
        print(event.source.user_id)
        print("================")

    # グループチャットなら groupId をログに出力
    if event.source.type == "group":
        print("=== Group ID ===")
        print(event.source.group_id)
        print("================")

    # GPT 呼び出し（OpenAI Python v1.x インターフェース）
    resp = client.chat.completions.create(
        model="gpt-4o-mini",
        messages=[{"role": "user", "content": event.message.text}]
    )
    reply_text = resp.choices[0].message.content

    # LINE へ返信
    line_api.reply_message(
        event.reply_token,
        TextSendMessage(text=reply_text)
    )

if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5000)
