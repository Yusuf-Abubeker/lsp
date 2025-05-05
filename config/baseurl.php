<?php
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$base_path = "/lsp/"; // change as needed
define('BASE_URL', $protocol . $host . $base_path);
?>
