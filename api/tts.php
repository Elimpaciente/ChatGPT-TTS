<?php
header('Content-Type: text/plain');
$input = $_GET['input'] ?? '';
$prompt = $_GET['prompt'] ?? '';
$voice = $_GET['voice'] ?? 'coral';
$vibe = $_GET['vibe'] ?? 'null';
if (empty($input) || empty($prompt)) {
    http_response_code(400);
    echo "Error: Missing required parameters 'input' and 'prompt'";
    exit;
}
$url = 'https://www.openai.fm/api/generate';
$boundary = '----WebKitFormBoundary' . bin2hex(random_bytes(8));
$data = "--$boundary\r\n";
$data .= "Content-Disposition: form-data; name=\"input\"\r\n\r\n";
$data .= "$input\r\n";
$data .= "--$boundary\r\n";
$data .= "Content-Disposition: form-data; name=\"prompt\"\r\n\r\n";
$data .= "$prompt\r\n";
$data .= "--$boundary\r\n";
$data .= "Content-Disposition: form-data; name=\"voice\"\r\n\r\n";
$data .= "$voice\r\n";
$data .= "--$boundary\r\n";
$data .= "Content-Disposition: form-data; name=\"vibe\"\r\n\r\n";
$data .= "$vibe\r\n";
$data .= "--$boundary--\r\n";
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $data,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "Content-Type: multipart/form-data; boundary=$boundary",
        "User-Agent: Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36",
        "Accept: */*",
        "Origin: https://www.openai.fm",
        "Sec-Fetch-Site: same-origin",
        "Sec-Fetch-Mode: cors",
        "Sec-Fetch-Dest: empty",
        "Referer: https://www.openai.fm/worker-444eae9e2e1bdd6edd8969f319655e70.js",
        "Accept-Encoding: gzip, deflate, br, zstd",
        "Accept-Language: en-GB,en-US;q=0.9,en;q=0.8,bn;q=0.7"
    ],
    CURLOPT_HEADERFUNCTION => function($curl, $header) {
        $len = strlen($header);
        $header = explode(':', $header, 2);
        if (count($header) < 2) return $len;
        $name = strtolower(trim($header[0]));
        $value = trim($header[1]);
        if (in_array($name, ['content-type', 'content-disposition', 'content-length'])) {
            header("$name: $value");
        }
        return $len;
    }
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
if (curl_error($ch)) {
    http_response_code(500);
    echo "cURL Error: " . curl_error($ch);
    curl_close($ch);
    exit;
}
curl_close($ch);
if ($httpCode === 200) {
    echo $response;
} else {
    http_response_code($httpCode);
    echo "Error: Received HTTP $httpCode from upstream API";
}
?>
