<?php

$envPath = getenv('GITHUB_EVENT_PATH'); //json
//ローカルにファイル存在しないので確認
if (empty($envPath) || file_exists($envPath)) {

    echo "GITHUB_EVENT_PATHが取得できません" . PHP_EOL;
}


$envJson = file_get_contents($envPath);

$envData = json_decode($envJson, true);



if (empty($envData)) {

    echo "ペイロードが取得できませんでした" . PHP_EOL;
}

$PRurl = $envData['pull_request']['url'];
$PRnum = $envData['number'];

echo "PRのURL:" . $PRurl . PHP_EOL;
echo "PRの番号:" . $PRnum . PHP_EOL;


//api認証
$apiKey = getenv('CLAUDE_API_KEY');
$token = getenv('ACCESS_TOKEN');

if (empty($apiKey)) {
    echo "キーが空です" . PHP_EOL;
}


$header = [

    "x-api-key:" . $apiKey,
    "anthropic-version: 2023-06-01",
    "content-type: application/json"

];

$body = [
    "model" => "claude-3-7-sonnet-20250219",
    "max_tokens" => 1024,
    "messages" => [
        [
            "role" => "user",
            "content" => "Hello, world"
        ]
    ]
];


$ch = curl_init('https://api.anthropic.com/v1/messages');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));

$response = curl_exec($ch); //json
$httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErro = curl_error($ch);
curl_close($ch);

if (!$response) {
    echo "レスポンスが空です" . PHP_EOL;
}


if ($curlErro) {
    echo "curlのエラーです" . "ステータス：" . $httpStatus . PHP_EOL;
} else {
    $responseData = json_decode($response, true); //配列
    echo "レスポンスの中身:" . $responseData; //表示されるか_
}
