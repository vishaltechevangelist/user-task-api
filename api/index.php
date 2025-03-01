<?php

//echo $_SERVER["REQUEST_URI"];
//echo "<br/>";
//echo parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$uri_parts = explode("/", $_SERVER["REQUEST_URI"]);
//print_r($uri_parts);
echo $uri_parts[2]. ",". $uri_parts[3];
echo $_SERVER["REQUEST_METHOD"];
