<?php
include __DIR__ . '/../config.php';  // 設定ファイルのインクルード

// ログファイルの保存先ディレクトリ
$log_dir = __DIR__ . '/../logs/';

// ログディレクトリが存在しない場合は作成
if (!is_dir($log_dir)) {
    mkdir($log_dir, 0777, true);
}

// エラーログと成功ログのファイルパスを設定
$error_log_file = $log_dir . 'error_log_' . date('Y-m-d') . '.log';
$success_log_file = $log_dir . 'success_log_' . date('Y-m-d') . '.log';

// 実行ファイル名を取得
$script_name = basename(__FILE__);

// 仮想環境内のPythonのパス
$venv_python = __DIR__ . '/../../api/venv/bin/python';
// Pythonスクリプトのパス
$python_script = __DIR__ . '/../../api/fetch_news.py';

// データベースへの接続
$conn = new mysqli($servername, $username, $password, $dbname);

// 接続を確認し、エラーがあればログに記録して終了
if ($conn->connect_error) {
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($error_log_file, "[$timestamp] [$script_name] Connection failed: " . $conn->connect_error . "\n", FILE_APPEND);
    die("Connection failed: " . $conn->connect_error);
}

try {
    // トランザクション開始
    $conn->begin_transaction();

    // 仮想環境のPythonでPythonスクリプトを実行
    $output = shell_exec("$venv_python $python_script");
    $news_articles = json_decode($output, true);

    $records_inserted = 0;

    if (is_array($news_articles)) {
        foreach ($news_articles as $article) {
            $title = $article['title'] ?? null;
            $url = $article['url'] ?? null;
            $summary = $article['summary'] ?? null;
            $published_at = $article['published_at'] ?? null;
            $category_code = $article['news_category_cd'] ?? null;

            // 現在のタイムスタンプを取得
            $timestamp = date('Y-m-d H:i:s');

            // 必要なデータがすべて存在するかチェック
            if ($title && $url && $summary && $published_at && $category_code) {
                // 重複チェックとprocessedフラグの取得
                $check_sql = "SELECT processed, news_category_cd FROM news_articles WHERE url = ?";
                $stmt = $conn->prepare($check_sql);
                $stmt->bind_param("s", $url);
                $stmt->execute();
                $result = $stmt->get_result();

                $processed = 0; // デフォルトは未処理
                $record_exists = false;

                while ($row = $result->fetch_assoc()) {
                    if ($row['news_category_cd'] === $category_code) {
                        $record_exists = true; // 同じURLで同じカテゴリがすでに存在する
                    }
                    if ($row['processed'] == 1) {
                        $processed = 1; // 同じURLで処理済みのレコードが存在する場合、フラグを1にする
                    }
                }

                if (!$record_exists) {
                    // 新しいレコードを挿入
                    $sql = "INSERT INTO news_articles (site_cd, title, url, summary, published_at, news_category_cd, created_at, processed, collection_method_cd)
                            VALUES ('01', ?, ?, ?, ?, ?, NOW(), ?, '01')";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sssssi", $title, $url, $summary, $published_at, $category_code, $processed);
                    $stmt->execute();
                    $stmt->close();

                    $records_inserted++;

                    file_put_contents($success_log_file, "[$timestamp] [$script_name] New record created successfully for URL: $url in category $category_code with processed = $processed\n", FILE_APPEND);
                }
            }
        }

        if ($records_inserted == 0) {
            // 新しいレコードが一度も挿入されなかった場合
            file_put_contents($success_log_file, "[$timestamp] [$script_name] No new records were created.\n", FILE_APPEND);
        }
    } else {
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($error_log_file, "[$timestamp] [$script_name] No valid data received from API\n", FILE_APPEND);
    }

    // トランザクションコミット
    $conn->commit();
} catch (Exception $e) {
    // エラー発生時にはトランザクションをロールバック
    $conn->rollback();
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($error_log_file, "[$timestamp] [$script_name] Transaction rolled back due to an error: " . $e->getMessage() . "\n", FILE_APPEND);
}

// データベース接続を閉じる
$conn->close();
?>
