<?php
include __DIR__ . '/../config.php';

// ログファイルの保存先
$log_dir = __DIR__ . '/../logs/';
// ログディレクトリが存在しない場合は作成
if (!is_dir($log_dir)) {
    mkdir($log_dir, 0777, true);
}
$error_log_file = $log_dir . 'error_log_' . date('Y-m-d') . '.log';
$success_log_file = $log_dir . 'success_log_' . date('Y-m-d') . '.log';

$result=array();
//APIキー
$apiKey='';

// 実行ファイル名の取得
$script_name = basename(__FILE__);

// データベースへの接続
$conn = new mysqli($servername, $username, $password, $dbname);

// 接続を確認
if ($conn->connect_error) {
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($error_log_file, "[$timestamp] [$script_name] Connection failed: " . $conn->connect_error . "\n", FILE_APPEND);
    die("Connection failed: " . $conn->connect_error);
}

// ニュースをランダムで取得
$today = date('Y-m-d');
$sql = "SELECT summary FROM news_articles WHERE news_category_cd = '01' AND DATE(published_at) = :today ORDER BY RAND() LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute(['today' => $today]);
$newsSummary = $stmt->fetchColumn();


$promptTemplate = <<<EOT
あなたはXのポスト投稿文を生成するAIです。以下のルールに従って文章を作成してください。
1.直接投稿が可能な形式で作成してください
2.文章は140文字以内にしてください
3.次のニュースに対する文章を生成してください: $newsSummary
4.文章内に感嘆符は付けない事
5.ハッシュタグは付けない事
EOT;

$sys_con="Twitterに直接投稿が可能な状態で、ユーザーが入力した内容に従って日本語で投稿文を作成してください。";
$req_question=$promptTemplate;
//openAIAPIエンドポイント
$endpoint='https://api.openai.com/v1/chat/completions';
$headers=array(
'Content-Type:application/json',
'Authorization:Bearer'.$apiKey
);
//リクエストのペイロード
$data=array(
'model'=>'gpt-4o-mini',
'messages'=>[
[
"role"=>"system",
"content"=>$sys_con
],
[
"role"=>"user",
"content"=>$req_question
]
],
'temperature'=>1.0
);
//cURLリクエストを初期化
$ch=curl_init();
//cURLオプションを設定
curl_setopt($ch,CURLOPT_URL,$endpoint);
curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch,CURLOPT_POST,true);
curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode($data));
curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
//APIにリクエストを送信
$response=curl_exec($ch);
//cURLリクエストを閉じる
curl_close($ch);
//応答を解析
$result=json_decode($response,true);
//生成されたテキストを取得
$text=$result['choices'][0]['message']['content'];

// 1. テキストのベクトル化 (仮にOpenAI Embeddings APIを使用)
function getVector($text, $apiKey) {
    $endpoint = 'https://api.openai.com/v1/embeddings';
    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ];
    $data = [
        'model' => 'text-embedding-ada-002', // Embedding用のモデル
        'input' => $text
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $endpoint);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    $result = json_decode($response, true);
    return $result['data'][0]['embedding'];
}

// 2. コサイン類似度の計算
function cosineSimilarity($vectorA, $vectorB) {
    $dotProduct = array_sum(array_map(function($a, $b) { return $a * $b; }, $vectorA, $vectorB));
    $magnitudeA = sqrt(array_sum(array_map(function($a) { return $a * $a; }, $vectorA)));
    $magnitudeB = sqrt(array_sum(array_map(function($b) { return $b * $b; }, $vectorB)));
    
    if ($magnitudeA == 0 || $magnitudeB == 0) {
        return 0; // ベクトルがゼロの場合、類似度は0とする
    }
    
    return $dotProduct / ($magnitudeA * $magnitudeB);
}

// 3. 過去のベクトルの取得と類似度の計算
$generatedVector = getVector($text, $apiKey);

$sql = "SELECT vector FROM x_vectors WHERE x_post_id IS NOT NULL";
$result = $conn->query($sql);

$similarityThreshold = 0.8; // 類似度のしきい値
$isSimilar = false;

while ($row = $result->fetch_assoc()) {
    $vector = json_decode($row['vector'], true); // JSONから配列にデコード
    $similarity = cosineSimilarity($generatedVector, $vector);
    
    if ($similarity > $similarityThreshold) {
        $isSimilar = true;
        break;
    }
}

if ($isSimilar) {
    // 再生成のロジック
    // 再生成されたテキストをベクトル化して、再度類似度をチェック
} else {
    // 生成されたテキストをDBに保存する
    try {
        $conn->begin_transaction();
        
        // x_posts テーブルへの挿入
        $sql = "INSERT INTO x_posts (x_post_id, user_id, created_at, text, processed) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $xPostId, $userId, $createdAt, $text, $processed);
        $stmt->execute();
        
        // x_vectors テーブルへのベクトル挿入
        $vectorJson = json_encode($generatedVector);
        $sql = "INSERT INTO x_vectors (x_post_id, vector, created_at) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $xPostId, $vectorJson, $createdAt);
        $stmt->execute();
        
        $conn->commit();
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($success_log_file, "[$timestamp] [$script_name] successfully.\n", FILE_APPEND);
    } catch (Exception $e) {
        $conn->rollback();
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($error_log_file, "[$timestamp] [$script_name] Transaction rolled back due to an error: " . $e->getMessage() . "\n", FILE_APPEND);
    }
}

// 接続を閉じる
$conn->close();
?>
