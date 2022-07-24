<?php

if (PHP_SAPI !== "cli") {
    die("can only run from cli");
}

$options = getopt("h:p:u:s:n:");
var_dump($options);

$configData = '<?php

require_once("cache.php");

define("INSTALLED", true);
define("DB_HOST", "'. $options['h'] .'");
define("DB_USER", "'. $options['u'] .'");
define("DB_PASS", "'. $options['s'] .'");
define("DB_PORT", "'. $options['p'] .'");
define("DB_NAME", "'. $options['n'] .'");
define("SECRET", "'. floor(microtime(true)) .'");
?>';

file_put_contents("config.php", $configData);
?>