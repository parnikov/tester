<?php
session_start();
$_SESSION = [];
use Tester as TS;
require __DIR__  . DIRECTORY_SEPARATOR . "classes" . DIRECTORY_SEPARATOR . "__autoload.php";
header("Location: ".TS\Helper::getCurDur());