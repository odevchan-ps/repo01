from openai import OpenAI
import json
import os
from dotenv import load_dotenv

# .envファイルから環境変数を読み込む
load_dotenv()

# OpenAI APIキーの取得
api_key = os.getenv("OPENAI_API_KEY")

# OpenAIクライアントのインスタンス化
client = OpenAI(api_key=api_key)

def generate_prompt(news_summary):
    """
    ニュースサマリを使用してプロンプトを生成します。
    """
    prompt_template = f"""
    あなたはプロのコメンテーターです。
    以下のニュース記事について様々な角度から115文字以内の文章で作成してください。
    文章はそのままの状態で投稿可能な形で生成してください。

    ニュースサマリ: {news_summary}
    """
    return prompt_template

def execute_model(prompt):
    """
    モデルを使用して指定されたプロンプトに対する応答を生成します。
    """
    response = client.chat.completions.create(
        model="gpt-4o-mini",  # 使用するモデルを指定
        messages=[
            {"role": "system", "content": "You are a helpful assistant."},
            {"role": "user", "content": prompt}
        ],
        max_tokens=100,
        temperature=0.7
    )

    # 生成されたテキストを返す
    return response.choices[0].message.content.strip()

def refine_model_output(text, url, max_iterations=3):
    """
    生成されたテキストをより簡潔で魅力的にするために再生成します。
    """
    for _ in range(max_iterations):
        refined_prompt = f"""
        以下の文章をより簡潔で魅力的にしてください。
        感嘆符、ハッシュタグ、絵文字は使用せずに生成してください。
        文章はそのままの状態で投稿可能な形で生成してください。
        適宜、改行を入れて文章全体で６～８行ほどの文章にして、見やすくしてください。
        文章は115文字以内に収めるのを厳守してください。
        文章の最後にあるURLは、今回作成した文章のあとに改行を2行挟んだ状態で追加してください。: {text}

        URL: {url}
        """
        response = execute_model(refined_prompt)
        if response != text:
            text = response
        else:
            break
    return text

def check_similarity(new_text, threshold=0.8):
    """
    新しいテキストが前回の出力と類似しているかをチェックします。
    """
    # 類似性チェックのロジックを実装
    return False

def main(news_summary, url):
    """
    ニュースサマリとURLを使用してモデルを実行し、結果を表示します。
    """
    prompt = generate_prompt(news_summary)
    initial_output = execute_model(prompt)
    refined_output = refine_model_output(initial_output, url)

    if check_similarity(refined_output):
        return main(news_summary, url)

    result = {
        "prompt_text": prompt,
        "initial_output": initial_output,
        "generated_text": refined_output
    }
    print(json.dumps(result, ensure_ascii=False, indent=2))

if __name__ == "__main__":
    import sys

    if len(sys.argv) > 2:
        news_summary = sys.argv[1]
        url = sys.argv[2]
        main(news_summary, url)
    else:
        print(json.dumps({"error": "No summary or URL provided"}))
