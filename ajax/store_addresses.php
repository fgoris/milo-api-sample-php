<?php
include '../settings.php';
header("Content-Type: application/json; charset=UTF-8");
$url = "https://api.x.com/milo/v3/store_addresses?key=" . API_KEY . "&" . $_SERVER['QUERY_STRING'];
$api_response = file_get_contents($url);
if ($api_response) {
    echo $api_response;
}
?>
