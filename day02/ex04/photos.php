#!/usr/bin/env php
<?php
libxml_use_internal_errors(true);
$ch = curl_init($argv[1]);
curl_setopt($ch, CURLOPT_HEADER, FALSE);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_BINARYTRANSFER, TRUE);
$data = curl_exec($ch);
$rurl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
curl_close($ch);

if ($data && ($dom = DOMDocument::loadHTML($data, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOERROR | LIBXML_NOWARNING))) {
    if (($url = parse_url($rurl))) {
        $dir = $url["host"];

        @mkdir($dir);

        foreach($dom->getElementsByTagName("img") as $img) {
            $src = $img->attributes->getNamedItem("src")->nodeValue;
            if (($src_url = parse_url($src))) {
                $filename = $src_url["path"];
                if (($pos = strrpos($filename, '/')))
                    $filename = substr($filename, $pos + 1);

                if (!array_key_exists("host", $src_url)) {
                    if ($src_url["path"][0] === '/')
                        $src = $url["scheme"] . "://" . $url["host"] . $src_url["path"];
                    else
                        $src = $url["scheme"] . "://" . $url["host"] . "/" . $src_url["path"];
                }
                $dst = $dir . DIRECTORY_SEPARATOR . $filename;
                @copy($src, $dst);
            } else
                echo "Malformed url ", $src, " ignoring\n";
        }
    } else
        echo "Invalid url\n";
} else
    echo "Invalid url or file\n";