<?php

$envPath = getenv('GITHUB_EVENT_PATH');
if (empty($envPath) || !file_exists($envPath)) {
    echo "GITHUB_EVENT_PATHが取得できませんでした" . PHP_EOL; 

}
$envJson = file_get_contents($envPath);
$envData = json_decode($envJson , true);
if (!isset($envData)) {
    echo "データの取得ができませんでした" . PHP_EOL;
}


$PRurl = $envData['pull_request']['url'];
$PRnum = $envData['number'];

echo "PRのURL:" . $PRurl . PHP_EOL;
echo "PRの番号:" . $PRnum . PHP_EOL;



//api認証
$apiKey = getenv('CLAUDE_API_KEY');
$token = getenv('token');

if (empty($apiKey)) {
    echo "APIのキーが不適切です" . PHP_EOL;
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

$response = curl_exec($ch); 
$httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);

curl_close($ch);

$responseData = json_decode($response, true);

if (!in_array($httpStatus, [200, 201, 202])) {
    echo "エラーです:" . $curlError . PHP_EOL;
    echo "ステータスコード:" . $httpStatus . PHP_EOL;
}

echo "成功";
echo json_encode($responseData , JSON_UNESCAPED_UNICODE);



