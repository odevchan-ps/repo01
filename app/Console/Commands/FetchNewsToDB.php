<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FetchNewsToDB extends Command
{
    // コマンド名
    protected $signature = 'fetch:news';

    // コマンドの説明
    protected $description = 'Fetch RSS news and store them into the database';

    // コンストラクタ
    public function __construct()
    {
        parent::__construct();
    }

    // コマンドの実行
    public function handle()
    {
        // 仮想環境内のPythonのパス
        $venv_python = base_path('api/venv/Scripts/python.exe');
        // Pythonスクリプトのパス
        $python_script = base_path('api/fetch_news.py');

        // shell_execでPythonスクリプトを実行し、その結果を取得
        $output = shell_exec($venv_python . ' ' . $python_script . ' 2>&1');

        // 出力内容をログに記録
        if (empty($output)) {
            Log::error('Python script did not return any output.');
            $this->error('Python script did not return any output.');
            return;
        }

        // Pythonからの出力をJSONとしてデコード
        $news_articles = json_decode($output, true);

        // JSONデコードに失敗した場合のエラーハンドリング
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('JSON decode error: ' . json_last_error_msg());
            $this->error('JSON decode error: ' . json_last_error_msg());
            return;
        }

        // データベースへの挿入処理
        foreach ($news_articles as $article) {
            try {
                DB::table('news_articles')->insert([
                    'title' => $article['title'],
                    'url' => $article['url'],
                    'summary' => $article['summary'],
                    'published_at' => $article['published_at'],
                    'news_category_cd' => $article['news_category_cd'],
                    'site_cd' => '01',  // 固定値
                    'created_at' => now(),
                    'collection_method_cd' => '01',  // 固定値
                ]);
            } catch (\Exception $e) {
                Log::error('Database insert error: ' . $e->getMessage());
                $this->error('Database insert error: ' . $e->getMessage());
            }
        }

        $this->info('News articles fetched and stored successfully.');
    }
}
