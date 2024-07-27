<?php
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $videoId = htmlspecialchars($_GET['id']);
    $filelink = 'https://strtape.cloud/v/' . $videoId . '/';


if (strpos($filelink, "strtape.cloud") !== false) {
    $useragent = "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.96 Safari/537.36";
    $head = array(
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
        'Accept-Language: en-US,en;q=0.5',
        'Accept-Encoding: deflate',
        'Connection: keep-alive',
        'Cache-Control: max-age=0',
        'Dnt: 1',
        'Authority: strtape.tech',
        'Sec-Fetch-Site: none',
        'Sec-Fetch-Mode: navigate',
        'Sec-Fetch-User: ?1',
        'Sec-Fetch-Dest: document',
        'Upgrade-Insecure-Requests: 1'
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $filelink);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
    curl_setopt($ch, CURLOPT_ENCODING, "");
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($ch, CURLOPT_TIMEOUT, 25);
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    
    $h = curl_exec($ch);
    curl_close($ch);
    
    $h = str_replace("\\", "", $h);
    
    if (preg_match('/(\/\/[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h, $s)) {
        $srt = "https:" . $s[1];
    }
    
    if (preg_match_all("/\(\'\w+\'\)\.innerHTML\s*\=\s*(.*?)\;/", $h, $m)) {
        $e1 = $m[1][count($m[1]) - 1];
        $e1 = str_replace("'", '"', $e1);
        $d = explode("+", $e1);
        $out = "";
        
        for ($k = 0; $k < count($d); $k++) {
            $s = trim($d[$k]);
            preg_match("/\(?\"([^\"]+)\"\)?(\.substring\((\d+)\))?(\.substring\((\d+)\))?/", $s, $p);
            
            if (isset($p[3]) && isset($p[5])) {
                $out .= substr(substr($p[1], $p[3]), $p[5]);
            } elseif (isset($p[3])) {
                $out .= substr($p[1], $p[3]);
            } else {
                $out .= $p[1];
            }
        }
        
        $link = $out . "&stream=1";
        
        if ($link[0] == "/") {
            $link = "https:" . $link;
        }
    }
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Player</title>
    <link href="https://vjs.zencdn.net/7.14.3/video-js.css" rel="stylesheet" />
    <link
      href="https://unpkg.com/@videojs/themes@1/dist/fantasy/index.css"
      rel="stylesheet"
    />
    <style>
        body {
            margin: 0;
            padding: 0;
        }
        
        .video-js {
            width: 100vw;
            height: 100vh;
        }
           .vjs-big-play-button {
            background-color: white;
            border-color: white;
        }

    </style>
</head>
<body>
    <?php if (isset($link) && !empty($link)): ?>
        <video id="player" class="video-js vjs-theme-fantasy vjs-big-play-centered" controls>
            <source src="<?php echo htmlspecialchars($link); ?>" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        <script src="https://vjs.zencdn.net/7.14.3/video.js"></script>
        <script>
            var player = videojs('player');
        </script>
    <?php else: ?>
        <p>No video available</p>
    <?php endif; ?>
</body>
</html>
