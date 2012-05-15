<?php
include '../settings.php';
$url = "https://api.x.com/milo/v3/availability?key=" . API_KEY . "&" . $_SERVER['QUERY_STRING'];
$handle = fopen($url, 'r');
if ($handle) {
    while (($buffer = fgets($handle)) !== false) {
        echo $buffer;
        ob_flush();
        flush();
    }
    fclose($handle);
}
?>
