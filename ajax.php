<?php
session_start();
use Tester as TS;
include "classes/__autoload.php";
header("Cache-Control: no-cache");
echo json_encode([
		"endQuestion" => TS\StackQuestions::isEndTimeQuestion(TS\StackQuestions::getCurId()),
		"endAll" => TS\StackQuestions::isEndTimeAll(),
		"overList" => TS\StackQuestions::getOverList()
	]);