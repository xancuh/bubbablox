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
			"Cookie: .ROBLOSECURITY=_|WARNING:-DO-NOT-SHARE-THIS.--Sharing-this-will-allow-someone-to-log-in-as-you-and-to-steal-your-ROBUX-and-items.|_A470BD0B181FACBB6B87F1D3A719E807AF6AA2BFEAA7C83CBCE262AE770F3053B767968D466E8FF61632593684A3F894D7E7D8AB11A384B7C8EDCC4725E6D6426F70A09A92FA46E6702628B86B3A5CA23A1FB49EBDA2E5EBA5E7B5A01BB139C5E46C16FDF9C7D5BE2D57346E444735247A39CC4A8B8D7D80EEF6EE403C8D2290F4B028119E678F9A6805E4CE167374DF7F83872A41706CF275697ED873F9C9CAB5CFEFCBA24BCDF4FF5F9CD2FA2B39538CAB465C016316EB70CA4F50E0BEE76B38391D4C92F7E58DBC46EB432F7CB0625B79B8A4B3C6AA8319AE99A1E6FD07744B9EF11A6256D29E0FD59E0E95CEFD58CA9D4F56E30BACCA570917C0B8A28FE37E51997514CEADEA928227B50C10BA67CC3E57C4009E26822D0EC72AD5216942056CCF836DE6F77397EEFAAB86A52C83C557035A31712303D1A718EEE6737AB6153111857B985216AB12A2AEE615D2ACA846E857DC66898A09D756DA415B99ED03343A815A098E663A097C7A5DEADAD4528A6C87CBAFBC92AFBEAED99D6D15A709F2CCDD79A584226870359E617DE4CA8B966B55391E12FD5DFE36E138CD072CE160F0BEFFE9FE2113A02E9C4F6F758D93CB2106633EA78666F225DE7F0E79F4AE9F11000EFF5231C2A187D5928C45DDDC83057FA5C6752B9281D725C8C3E539C63B19B70AD8C42A2AFF234E6C0B7FE8A92547CDD99CEC47B67537580726757BD9A2D833F7A20460C361902ABBDAD01229D03DED541E4E4A50BD612D7CD8123B9924B609D1F9402ECC0998DE07C73778D41400E66C2D1481E4E31475CA4284A6D8A912FE7893B73D6F1E3BC704882CB3EE50D2947121D34414CA35785807D05715777F538E98A54AE1BCA08F98CDFCCE77B8713A665D1B86570890363022EA7FB57A8E2FF169843E9A513C11388F001B327E80B0E0168AFFF7E347CA5932CA387C44E01804D433EDC05435BA7174829C84997E348A7459509A45A1FADA8475332C9CAAE0C2F96EEA1885AED4F0F4D055CF4B2012B1E30508D6791CD6883A8D8130BDAB3C6E26594B74E04A63025196F596D0837BF213A35E5DFB966436A11CA05063C767E2152FBA6D8F1B50CA08E750272FDB0F"
        ]
    ],
    [
        'url' => "https://assetdelivery.roblox.com/v1/asset/?id=" . $id,
        'headers' => [
            "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36",
            "Accept: */*"
        ]
    ],
    [
        'url' => "https://bb.zawg.ca/asset/?id=" . $id,
        'headers' => [
            "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
            "Accept-Encoding: gzip, deflate, br",
            "Accept-Language: en-US,en;q=0.9",
            "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36",
            "Sec-Fetch-Dest: document",
            "Sec-Fetch-Mode: navigate",
            "Sec-Fetch-Site: none"
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
echo "Failed to fetch from BubbaBlox/Roblox";
exit();
?>