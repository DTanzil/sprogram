<?php

$url = __DIR__ . "/../../";
echo "__DIR__: " . $url . "    ";
$url = str_replace('C:\\xampp\htdocs\\', "http://localhost/", $url);
echo "str_replace: " . $url;

$host = gethostname();
$ip = gethostbyname($host);
echo "hostname: " . $host . "    ";
echo "IP: " . $ip . "    ";
echo "LocalhostTest: " . gethostbyname('localhost') . "    ";
echo "$ SERVER[SERVER_ADDR]: " . $_SERVER['SERVER_ADDR'] . "    ";
echo "$ SERVER[REMOTE_ADDR]: " . $_SERVER['REMOTE_ADDR'] . "    ";

echo '\n';

$bad = "<script>alert('hello');</script>";
echo "BAD: {$bad}";

//echo $_SERVER['DOCUMENT_ROOT'];
echo $_SERVER['REQUEST_URI'];
$clean 
?>