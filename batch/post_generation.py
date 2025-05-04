# post_generation.py

import os
from dotenv import load_dotenv
from openai import OpenAI

load_dotenv()
client = OpenAI(api_key=os.getenv("OPENAI_API_KEY"))

def generate_post_text(prompt_text):
    """
    x_prompts.prompt_text を受け取り、
    投稿向けの短文を返す
    """
    try:
        completion = client.chat.completions.create(
            model="gpt-3.5-turbo",
            messages=[
                {"role":"user", "content": prompt_text}
            ],
            max_tokens=100,
            temperature=0.8
        )
        return completion.choices[0].message.content.strip()
    except Exception as e:
        print(f"OpenAI API error (post): {e}")
        return None
