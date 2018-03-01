<?php 
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'youtubeui_1');
define('JSON_OPTIONS', JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
define('JSON_TYPE', 'application/json');
define('HTTP_PATH', 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . str_replace('/index.php', '/', $_SERVER['PHP_SELF']));