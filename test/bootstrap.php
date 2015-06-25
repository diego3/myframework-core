<?php


define('TEST_OR_INSTALL', true);

define("PATH_LOCAL", realpath(__DIR__));//"D:\site\www\myframework\test"

//
//require_once "D:/site/www/myframework/vendor/autoload.php";
require_once "/../vendor/autoload.php";

//funções utilitárias utilizadas nos testes
require_once "src/testfunctions.php";
require_once '/../src/mycore.php';