<?php
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id <= 0) {
    http_response_code(400);
    header("Content-Type: text/plain");
    echo "Invalid asset ID";
    exit();
}

$cachedir = rtrim($_SERVER["DOCUMENT_ROOT"], '/') . '/Asset/assets/';
if (!file_exists($cachedir)) {
    if (!mkdir($cachedir, 0755, true)) {
        http_response_code(500);
        echo "Failed to create cache directory";
        exit();
    }
}

$cachedfile = $cachedir . $id;

if (file_exists($cachedfile) && filesize($cachedfile) > 0) {
    serve($cachedfile);
    exit();
}

function serve($file) {
    $etag = '"' . md5_file($file) . '"';
    $lastModified = gmdate('D, d M Y H:i:s T', filemtime($file));

    $clientEtag = $_SERVER['HTTP_IF_NONE_MATCH'] ?? '';
    $clientModified = $_SERVER['HTTP_IF_MODIFIED_SINCE'] ?? '';
    
    if ($clientEtag === $etag || $clientModified === $lastModified) {
        http_response_code(304);
        exit();
    }
    
    $headers = [
        "Accept-Ranges" => "bytes",
        "Access-Control-Allow-Methods" => "GET",
        "Access-Control-Allow-Origin" => "*",
        "Cache-Control" => "public, max-age=31535977",
        "Content-Type" => getmime($file),
        "Content-Disposition" => 'attachment; filename="asset"',
        "Content-Length" => filesize($file),
        "ETag" => $etag,
        "Last-Modified" => $lastModified,
        "Timing-Allow-Origin" => "*",
        "Vary" => "Accept-Encoding"
    ];
    
    foreach ($headers as $key => $value) {
        header("$key: $value");
    }

    ob_clean();
    flush();
    
    $handle = fopen($file, 'rb');
    if ($handle) {
        while (!feof($handle)) {
            echo fread($handle, 8192);
            flush();
        }
        fclose($handle);
    } else {
        readfile($file);
    }
}

function getmime($file) {
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file);
        finfo_close($finfo);
        if ($mimeType) {
            return $mimeType;
        }
    }
    return "application/octet-stream";
}

function fetchandcache($url, $customHeaders = [], $timeout = 30) {
    global $cachedfile;
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 5,
        CURLOPT_TIMEOUT => $timeout,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
        CURLOPT_ENCODING => '',
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1
    ]);
    
    if (!empty($customHeaders)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $customHeaders);
    }
    
    $fileData = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        error_log("cURL error for $url: $error");
        return false;
    }

    if ($httpCode !== 200 || empty($fileData) || strlen($fileData) < 10) {
        error_log("bad response from $url: HTTP $httpCode, size: " . strlen($fileData));
        return false;
    }

    $tempFile = $cachedfile . '.tmp';
    if (file_put_contents($tempFile, $fileData, LOCK_EX) === false) {
        error_log("failed to make temp file: $tempFile");
        return false;
    }
    
    if (filesize($tempFile) !== strlen($fileData)) {
        unlink($tempFile);
        error_log("size mismatch: $tempFile");
        return false;
    }
    
    if (!rename($tempFile, $cachedfile)) {
        unlink($tempFile);
        error_log("failed to cache: $cachedfile");
        return false;
    }

    serve($cachedfile);
    return true;
}

$sources = [
    [
        'url' => "https://assetdelivery.roblox.com/v1/asset/?id=" . $id,
        'headers' => [
            "User-Agent: Roblox/WinInet",
            "Accept: */*",
            "Accept-Encoding: gzip, deflate, br",
			"Cookie: YourRobloSecurityHere"
        ]
    ],
    [
        'url' => "https://assetdelivery.roblox.com/v1/asset/?id=" . $id,
        'headers' => [
            "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36",
            "Accept: */*"
        ]
    ]
];

foreach ($sources as $source) {
    if (fetchandcache($source['url'], $source['headers'])) {
        exit();
    }
    usleep(100000);
}

http_response_code(404);
header("Content-Type: text/plain");
echo "Failed to fetch from Roblox";
exit();
?>