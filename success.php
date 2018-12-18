<?php
session_start();

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
<div class="container">
	<div class="row">
		<div class="col"><h1>Вы прошли тест</h1></div>
	</div>
	<div class="container">
		<div class="row">
			Для продолжения работы смените контрольную сумму массива данных или перейдите по&nbsp;
			<a href="<?=str_replace("success.php", "", $_SERVER["PHP_SELF"])?>clear.php">
				ссылке
			</a>
		</div>
		<div class="row mt-2">
			<div class="col"><h2>Результаты</h2></div>
		</div>
		<?php
		if( empty( $_SESSION["END_TIME"]) ){
			$_SESSION["END_TIME"] = time();
		}

		$ans["time_start"] = $_SESSION["QUESTIONS_LOAD_TIME"];
		$ans["time_stop"] = $_SESSION["END_TIME"];
		foreach ( $_SESSION["ANSWERS"] as $key => $arItem){
			$ans[$key]=$arItem;
		}?>
		<div class="row">
			<?php
			echo "<pre>";
			print_r($ans);
			echo "</pre>";
			?>
		</div>
	</div>
</div>
</body>
</html>