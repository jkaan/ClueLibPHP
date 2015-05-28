<?php
$url = trim($argv[1], "'");

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_FAILONERROR => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_POST => true
]);
curl_exec($ch);