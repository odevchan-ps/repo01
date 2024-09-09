<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\TaskTimeline;
use Carbon\Carbon;

class NewsImportService
{
    public function importNews($taskName)
    {
        Log::info("Starting news import process for task: $taskName");

        $timeline = TaskTimeline::create([
            'task_name' => $taskName,
            'status' => 'in_progress',
            'start_time' => Carbon::now(),
            'operation_type' => 'fetch',
        ]);

        $startTime = Carbon::now();
        $addedArticleIds = [];
        $recordCount = 0;

        try {
            Log::info("Executing Python script");

            // Pythonスクリプトの実行
            $venv_python = base_path('api/venv/Scripts/python.exe');
            $python_script = base_path('api/fetch_news.py');
            $output = shell_exec($venv_python . ' ' . $python_script . ' 2>&1');

            Log::info("Python script output: $output");

            if ($output === null || empty($output)) {
                Log::error("Python script returned no output");
                throw new \Exception('Python script did not return any output.');
            }

            $output = mb_convert_encoding($output, 'UTF-8', 'auto');
            $news_articles = json_decode($output, true);

            Log::info("Decoded news articles: " . json_encode($news_articles));

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error("JSON decode error: " . json_last_error_msg());
                throw new \Exception('JSON decode error: ' . json_last_error_msg());
            }

            // データベースへの挿入処理
            foreach ($news_articles as $article) {
                Log::info("Checking for existing article with URL: " . $article['url']);
                // 重複チェック
                $exists = DB::table('news_articles')
                    ->where('url', $article['url'])
                    ->where('news_category_cd', $article['news_category_cd'])
                    ->exists();

                if (!$exists) {
                    $article_id = DB::table('news_articles')->insertGetId([
                        'title' => $article['title'],
                        'url' => $article['url'],
                        'summary' => $article['summary'],
                        'published_at' => $article['published_at'],
                        'news_category_cd' => $article['news_category_cd'],
                        'site_cd' => '01',  // 固定値
                        'created_at' => now(),
                        'collection_method_cd' => '01',  // 固定値
                    ]);

                    Log::info("Inserted new article with ID: $article_id");

                    $addedArticleIds[] = $article_id;
                    $recordCount++;
                } else {
                    Log::info("Duplicate article found, skipping: " . $article['url']);
                }
            }

            // タイムライン更新
            $endTime = Carbon::now();
            Log::info("Updating timeline with record count: $recordCount and article IDs: " . implode(',', $addedArticleIds));

            Log::info('Updating timeline with data: ', [
                'status' => 'success',
                'end_time' => $endTime,
                'duration' => $endTime->diffInSeconds($startTime),
                'affected_ids' => implode(',', $addedArticleIds),
                'record_count' => $recordCount,
                'additional_info' => 'NHK RSS fetched and stored successfully.'
            ]);            

            $timeline->update([
                'status' => 'success',
                'end_time' => $endTime,
                'duration' => $endTime->diffInSeconds($startTime),
                'affected_ids' => implode(',', $addedArticleIds),
                'record_count' => $recordCount,
                'additional_info' => 'NHK RSS fetched and stored successfully.',
            ]);

            Log::info("News import process completed successfully");

            return true;

        } catch (\Exception $e) {
            $endTime = Carbon::now();
            Log::error("Error occurred during news import: " . $e->getMessage());

            $timeline->update([
                'status' => 'failed',
                'end_time' => $endTime,
                'duration' => $endTime->diffInSeconds($startTime),
                'error_message' => $e->getMessage(),
            ]);

            return false;
        }
    }
}