<?php


define('TEST_OR_INSTALL', true);

define("PATH_LOCAL", realpath(__DIR__));//"D:\site\www\myframework\test"
define("PATH_MYFRAME", "C:\www\htdocs\myframework-core");
define("PATH_APP", PATH_LOCAL . "/../app");
define("PATH_DEFAULT", PATH_LOCAL . '/../app_default');

define("SERVER_MODE", "DEVELOPMENT");

//require_once "D:/site/www/myframework/vendor/autoload.php";
require_once "/../vendor/autoload.php";

//funções utilitárias utilizadas nos testes
require_once "src/testfunctions.php";
require_once '/../src/mycore.php';

//classe base para os datatypes do framework
require_once "src/DataType/DatatypeBaseTest.php";


$databaseconfig = getConfig(PATH_LOCAL . "/conf/database.local.ini");

define("DATABASE_DRIVER", getValueFromArray($databaseconfig['database'], 'driver'), '');
define("DATABASE_NAME", getValueFromArray($databaseconfig['database'], 'dbname', ''));
define("DATABASE_HOST", getValueFromArray($databaseconfig['database'], 'host', ''));
define("DATABASE_PORT", getValueFromArray($databaseconfig['database'], 'port', ''));
define("DATABASE_USER", getValueFromArray($databaseconfig['database'], 'user', ''));
define("DATABASE_PASSWORD", getValueFromArray($databaseconfig['database'], 'password'), '');


//Error lib - http://logging.apache.org/log4php/quickstart.html
Logger::configure(PATH_LOCAL . '/conf/php4log.xml');