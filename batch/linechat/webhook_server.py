from flask import Flask, request, abort
from linebot import LineBotApi, WebhookHandler
from linebot.exceptions import InvalidSignatureError
from linebot.models import MessageEvent, TextMessage, TextSendMessage
import openai
import config

app = Flask(__name__)
handler = WebhookHandler(config.LINE_SECRET)
line_api = LineBotApi(config.LINE_TOKEN)
openai.api_key = config.OPENAI_KEY

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
        # グループからのメッセージなら groupId を出力
    if event.source.type == "group":
        print("=== Group ID ===")
        print(event.source.group_id)
        print("================")
    resp = openai.ChatCompletion.create(
        model="gpt-4o-mini",
        messages=[{"role":"user", "content": event.message.text}]
    )
    reply_text = resp.choices[0].message.content
    line_api.reply_message(
        event.reply_token,
        TextSendMessage(text=reply_text)
    )

if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5000)
