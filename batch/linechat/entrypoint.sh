#!/bin/bash
# スケジューラをバックグラウンド起動
python /batch/linechat/push_scheduler.py &
# Webhook サーバを起動
python /batch/linechat/webhook_server.py
