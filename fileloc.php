<?php
define("CONFIG_FILE_LOCATION", dirname(__FILE__).'/config.php');
define("TMP_CACHE_LOCATION", dirname(__FILE__).'/tmp/cache');
define("TMP_TEMPLATES_C_LOCATION", dirname(__FILE__).'/tmp/templates_c');
#define("LOG4PHP_CONFIGURATION", dirname(__FILE__).'/lib/log4php/log4php.xml');
define("LOG4PHP_LOGDIR", dirname(__FILE__).'/tmp/cache');
require_once(dirname(__FILE__).'/lib/log4php/LoggerManager.php');
?>
