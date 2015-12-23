<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('error_reporting', 32767);
ini_set("error_log", $_SERVER['DOCUMENT_ROOT']."logs/php_error.log");

$username = '6PvP1f1tv5V7UInw';
$password = 'GJ5t8GTGLLSIPEdTx39orItDchWcjj5WXxudPeNct7BM2Jzm';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://192.168.1.159:82/api/v1/api.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
$output = curl_exec($ch);
$info = curl_getinfo($ch);
curl_close($ch);
print_r($output);
?>