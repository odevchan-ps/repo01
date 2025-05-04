# prompt_generation.py

import os
from openai import OpenAI
from dotenv import load_dotenv

load_dotenv()
client = OpenAI(api_key=os.getenv("OPENAI_API_KEY"))

def generate_prompt(news_summary: str) -> str:
    """
    ニュースサマリをもとに、115文字以内の投稿文のプロンプトを作成。
    """
    return f"""
あなたはプロのコメンテーターです。
以下のニュース記事について様々な角度から115文字以内の文章で作成してください。
文章はそのままの状態で投稿可能な形で生成してください。

ニュースサマリ: {news_summary}
"""

def execute_model(prompt: str, model: str = "gpt-4o-mini") -> str:
    """
    指定モデルで Chat Completion を呼び出し、応答テキストを返す。
    """
    result = client.chat.completions.create(
        model=model,
        messages=[
            {"role": "system", "content": "You are a helpful assistant."},
            {"role": "user",   "content": prompt}
        ],
        max_tokens=100,
        temperature=0.7
    )
    return result.choices[0].message.content.strip()

def refine_model_output(text: str, url: str, model: str = "gpt-4o-mini", max_iterations: int = 1) -> str:
    """
    初回生成テキストをリファインし、最後に改行2行＋URLを付与して返却。
    """
    for _ in range(max_iterations):
        refined_prompt = f"""
以下の文章をより簡潔で魅力的にしてください。
感嘆符、ハッシュタグ、絵文字は使用せずに生成してください。
文章はそのままの状態で投稿可能な形で生成してください。
適宜、改行を入れて文章全体で６～８行ほどの文章にして、見やすくしてください。
文章は115文字以内に収めるのを厳守してください。
文章の最後にあるURLは、今回作成した文章のあとに改行を2行挟んだ状態で追加してください。

{text}

URL: {url}
"""
        new_text = execute_model(refined_prompt, model=model)
        if new_text == text:
            break
        text = new_text

    return f"{text}\n\n{url}"

def generate_post_text(prompt_text: str, url: str, model: str = "gpt-4o-mini") -> str:
    """
    ① prompt_text で初回生成 → ② リファイン → 投稿文を返す
    """
    initial = execute_model(prompt_text, model=model)
    
    final = initial
    # final   = refine_model_output(initial, url, model=model)

    return final
