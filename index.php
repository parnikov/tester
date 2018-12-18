<?php
session_start();
use Tester as TS;
// основные функции
require __DIR__  . DIRECTORY_SEPARATOR . "classes" . DIRECTORY_SEPARATOR . "__autoload.php";
if(!empty($_SESSION["END_TIME"])){
	header("Location: ". str_replace("index.php", "success.php", $_SERVER["PHP_SELF"] ));
}
?><!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="css/bootstrap.min.css" >
	<link rel="stylesheet" href="css/bootstrap-grid.css" >
	<link rel="stylesheet" href="css/style.css" >
	<script src="js/jquery-3.3.1.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/cleave.min.js"></script>
	<script src="js/script.js"></script>
	<meta name="viewport"
		  content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Опросник</title>
</head>
<body>
<?php
// данные
require __DIR__  . DIRECTORY_SEPARATOR . "data.php";
if( !empty($data["user"]) && !empty($data["qw"]) ){
	$user = new TS\User($data["user"]);
	$question= new TS\Question($data);
}else{
	die("Некорректно сформированы данные");
}
?>
<div class="container">
	<div class="row">
		<div class="col"><h1><?=(!empty($data["test_name"]) ? $data["test_name"] : "Тест")?></h1></div>
	</div>
	<div class="row alert alert-secondary">
		<div class="col-3">
			Данные о пользователе
		</div>
		<div class="col-9">
			<div class="container">
				<div class="row">
					<div class="col">Ф.И.О. : <?=$user->getFio()?></div>
					<div class="col">Группа : <?=$user->getGr()?></div>
				</div>
			</div>
		</div>
	</div>
	<div class="row alert alert-secondary">
		<div class="col-3">
			Время на выполнение теста
		</div>
		<div class="col-9">
			<div class="container">
				<div class="row">
					<div class="col">
						Оставшиеся время на тест : <span id="timeAll" data-time="<?=TS\StackQuestions::getTimeRemaining()?>"></span>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row alert">
		<div class="col-4">
			Вопрос
		</div>
		<div class="col-4">
			Ответ на вопрос
		</div>
		<div class="col-4">
			Время на вопрос
		</div>
	</div>
	<div class="row">
		<div class="col-4 alert alert-info" id="question"><?=$question->getText()?></div>
		<div class="col-4" id="answer">
			<form action="<?=$_SERVER["PHP_SELF"]?>" method="post" class="container">
				<div class="form-group">
				<?php
				if(is_array($question->getErrors())){
					foreach ( $question->getErrors() as $error ){?>
						<div class="alert alert-danger"><?=$error?></div>
						<?php
					}
				}
				if(!empty($question->getInfo())){
					echo $question->getInfo();
				}?>
				</div>
				<div class="form-group" >
				<?=$question->getTemplateAnswer()?>
				</div>
				<div class="form-group" id="status">
				<?php if( !$question->isEndTime() ){?>
					<input type="submit" class="btn btn-primary"
						   value="<?=($question->isLast()) ? "Ответить и завершить тест" : "Ответить"?>">
				<?php  }else{
					if( TS\StackQuestions::isEndTimeAll() ){?>
						<div class="mt-3">
							<a href="<?=TS\Helper::getCurDur()?>success.php" class="btn btn-primary" >Завершить тест</a>
						</div>
					<?php }else{?>
						Время отведенное на вопрос вышло,<br> выберите вопросы из списка
					<?php }?>
				<?php }?>
				</div>
				<input type="hidden" value="1" name="send">
			</form>
		</div>
		<div class="col-4<?=TS\StackQuestions::isEndTimeAll() ? " alert-danger" : ""?>" id="questionBox">
		<?php if( !TS\StackQuestions::isEndTimeAll() ){
			if($question->getMaxTime()){?>
				<?php if( $question->getRemainingTime() ){?>
					<div>Оставшееся время на вопрос : <span id="time" data-time="<?=$question->getRemainingTime()?>"></span></div>
				<?php }else{?>
					<div>Время отведенное на вопрос вышло,
						выберите вопросы из списка</div>
				<?php }
			}else{?>
				<div>Время ограничено общим временем на тест</div>
			<?php }

		}else{?>
			<div class="alert">Время отведенное на тест вышло</div>
		<?php }?>
		</div>
		<?php if( !TS\StackQuestions::isEndTimeAll() ) {
			TS\Helper::showNavString();
		}?>
	</div>
</div>
</body>
</html>
