from apscheduler.schedulers.blocking import BlockingScheduler
from linebot import LineBotApi
from linebot.models import TextSendMessage
import config

line_api = LineBotApi(config.LINE_TOKEN)

def send_push(msg):
    line_api.push_message(config.TARGET_ID, TextSendMessage(text=msg))

if __name__ == "__main__":
    sched = BlockingScheduler(timezone="Asia/Tokyo")
    for h in [6, 12, 18, 21, 23]:
        sched.add_job(
            send_push, "cron",
            hour=h, minute=0,
            args=[f"{h}時の定時通知です"]
        )
    sched.start()
