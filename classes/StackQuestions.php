<?php

namespace Tester;


class StackQuestions
{
	/**
	 * Получаем хеш входного массива
	 * @return string - Хеш массива
	 */
	public static function getHashSum(){
		return !empty($_SESSION["QUESTION_HASH"]) ? $_SESSION["QUESTION_HASH"] : "";
	}
	/**
	 * Устанавливает хеш входного массива и сохраняет значение
	 * @param string $data
	 */
	private function setHashSum($data){
		if( !empty($_SESSION["QUESTION_BLOCK"]) &&  in_array(self::calculateHash($data),$_SESSION["QUESTION_BLOCK"])){
			die("Попытка сдать тест второй раз");
		}
		// показ первого вопроса
		$_SESSION["QUESTIONS_LOAD_TIME"] = $data["time_start"];
		// максимальное время теста
		$_SESSION["QUESTIONS_MAX_TIME"] = $data["time_max"];
		$_SESSION["QUESTION_HASH"] = self::calculateHash($data);
		$_SESSION["ANSWERS"] = [];
		$keys = array_keys($data["qw"]);
		// устанвливаем первый элемент массива как первый вопрос
		$_SESSION["QUESTION_ID"] = $keys[0];
		self::setItem($keys[0]);
		$_SESSION["CURRENT_QUESTION"] = self::getItem();
	}
	/**
	 * Синглтон
	 * @param array $data
	 * @return StackQuestions
	 */
	static function instance($data = [] ){
		return new StackQuestions($data);
	}
	/**
	 * Операции инициализации данных, на основе входных данных
	 * @param $data
	 */
	private static function initData($data){
		if( !$data ){
			global $data;
		}
		if(!empty($data["qw"])){
			self::setHashSum($data);
			self::setPool($data["qw"]);
			self::setItem();
		}else{
			die("Не задан массив с данными");
		}
	}
	/**
	 * StackQuestion constructor.
	 * @param array $data
	 */
	public function __construct($data = [] ){
		if( !$data ){
			global $data;
		}
		// проверки корректности входных данных
		if( !empty($data["qw"]) && is_array($data["qw"]) ){
			if( !self::getHashSum() || ( self::getHashSum() != $this->calculateHash($data) )
				|| empty($_SESSION["QUESTIONS_LIST"])){
				// установка данных
				self::initData($data);
				header("Location: " . $_SERVER["PHP_SELF"]);
			}
			// запоминаем вопрос в системе
			self::setItem();
		}else{
			die("Не задан массив с данными");
		}
	}
	/**
	 * Получение id вопроса
	 * @return mixed
	 */
	public static function getCurId(){
		return $_SESSION["QUESTION_ID"];
	}
	/**
	 * Проверка дан ли ответ на вопрос
	 * @param $idQw
	 * @return bool
	 */
	public static function isDone($idQw){
		return isset($_SESSION["ANSWERS"][$idQw]["val"]) ? true : false;
	}
	/**
	 * Получение значения ответа
	 * @return string
	 */
	public static function getCurValue(){
		return isset($_SESSION["ANSWERS"][$_SESSION["QUESTION_ID"]]["val"]) ? $_SESSION["ANSWERS"][$_SESSION["QUESTION_ID"]]["val"] : "";
	}
	/**
	 * Вычисление хеша, на основе основных данных
	 * @param $data
	 * @return string
	 */
	public function calculateHash($data){
		$data = [$data["user"], $data["qw"], $data["test_name"], $data["time_max"]];
		return md5(serialize($data));
	}
	/**
	 * Задание массива вопросов в сессию
	 * @param $data
	 */
	private static function setPool($data){
		if( !empty($data) && is_array($data)){
			$_SESSION["QUESTIONS_LIST"] = $data;
		}else{
			$_SESSION["QUESTIONS_LIST"] = [];
		}
	}
	/**
	 * Задание вопроса в системе по id
	 * @param null $id
	 */
	private static function setItem( $id = null ){
		if( empty( $id )  && ( !empty($_SESSION["QUESTION_ID"]) || !empty($_REQUEST["id"]) )){
			$id = !empty( $_REQUEST["id"] ) ? $_REQUEST["id"] : $_SESSION["QUESTION_ID"];
		}
		$id = abs((int) $id);
		$_SESSION["QUESTION_ID"] = $id;
		if( empty( $_SESSION["QUESTIONS_LIST"][$_SESSION["QUESTION_ID"]]["START_TIME"] )){
			$_SESSION["QUESTIONS_LIST"][$_SESSION["QUESTION_ID"]]["START_TIME"] = time();
		}
	}
	/**
	 * Установка следующего неотвеченного вопроса
	 * return void
	 */
	public static function next(){
		ob_start();
		$questionsIds = array_keys($_SESSION["QUESTIONS_LIST"]);
		$answersIds = array_keys($_SESSION["ANSWERS"]);
		$arDiff = array_diff($questionsIds, $answersIds);
		if( $arDiff ){
			$idQ = null;
			$noAnswQuestions = array_values( $arDiff );
			foreach ( $noAnswQuestions as $questionId){
				if( ! self::isEndTimeQuestion($questionId) ){
					$idQ = $questionId;
					break;
				}
			}
			$_SESSION["QUESTION_ID"] = $idQ;
			header("Location: ". $_SERVER["PHP_SELF"]);
		}else{
			$_SESSION["QUESTION_BLOCK"][] = $_SESSION["QUESTION_HASH"];
			$_SESSION["QUESTION_HASH"] = "";
			$_SESSION["ANSWERS"] = [];
			$_SESSION["QUESTION_ID"] = null;
			header("Location: ". str_replace("index.php", "success.php", $_SERVER["PHP_SELF"] ));
		}
		ob_end_clean();
		exit;
	}
	/**
	 * Получение массива данных по id вопроса
	 * @param null $id
	 * @return array
	 */
	public static function getItem( $id = null ){
		if( empty( $id )  && ( !empty($_SESSION["QUESTION_ID"]) || !empty($_REQUEST["id"]) )){
			$id = !empty( $_REQUEST["id"] ) ? $_REQUEST["id"] : $_SESSION["QUESTION_ID"];
		}
		$id = abs((int) $id);
		if( $id && !empty($_SESSION["QUESTIONS_LIST"][$id])){
			return $_SESSION["QUESTIONS_LIST"][$id];
		}
		return [];
	}
	/**
	 * Получение полного списка вопросов
	 * @return mixed
	 */
	public static function getItems(){
		return $_SESSION["QUESTIONS_LIST"];
	}
	/**
	 * Получение выделенного времении на вопрос в секундах, 0 - безлимит
	 * @return int
	 */
	public static function getEndTime(){
		if( !empty( $_SESSION["QUESTIONS_LIST"][$_SESSION["QUESTION_ID"]]["time"] ) ){
			return $_SESSION["QUESTIONS_LIST"][$_SESSION["QUESTION_ID"]]["time"];
		}else{
			return 0;
		}
	}
	/**
	 * Проверка, - есть ли еще время для ответа
	 * @param $id
	 * @return bool
	 */
	public static function isEndTimeQuestion( $id ){
		if( !empty($id) ){
			$remainingTime = 0;
			$question = self::getItem($id);
			$showQuestionTime = !empty( $question["START_TIME"] ) ? $question["START_TIME"] : 0;
			if( $showQuestionTime && $question["max_time"] != 0 ){
				$remainingTime = $question["max_time"] - ( time() - $showQuestionTime );
			}elseif( $question["max_time"] == 0 ){
				$remainingTime = $_SESSION["QUESTIONS_MAX_TIME"] - ( time() - ($_SESSION["QUESTIONS_LOAD_TIME"] + 100) ) ;
			}elseif( $showQuestionTime == 0 ){
				return false;
			}
			if( $remainingTime > 0){
				return false;
			}
		}
		return true;
	}
	/**
	 * Получаем список вопросов, у которых вышло время
	 * @return array
	 */
	public static function getOverList(){
		$arr = [];
		foreach ( $_SESSION["QUESTIONS_LIST"] as $id => $question){
			if( self::isEndTimeQuestion($id) ){
				$arr[] = $id;
			}
		}
		return $arr;
	}
	/**
	 * Проверка на последний неотвеченный вопрос
	 * @return bool
	 */
	public static function isLastQuestion(){

		if( self::isEndTimeAll() ){
			return true;
		}

		if( empty($_SESSION["QUESTIONS_LIST"] ) ) return true;

		$unit = [];

		foreach ( $_SESSION["QUESTIONS_LIST"] as $id => $question){
			// для безлимитных вотпросов
			if( $question["max_time"] == 0 ){
				// неотвеченный
				if( !isset($_SESSION["ANSWERS"][$id]) ){
					$unit[] = $id;
				}

			}elseif( !empty( $question["START_TIME"] ) ){
				$remainingTime = $question["max_time"] - (time() - $question["START_TIME"]);

				// неотвеченный
				if( $remainingTime > 0 && !isset($_SESSION["ANSWERS"][$id])){
					$unit[] = $id;
				}
			}else{
				$unit[] = $id;
			}
		}
		$checkLogic = count($unit) == 1 && $unit[0] == $_SESSION["QUESTION_ID"];
		return $checkLogic ? true : false;
	}
	/**
	 * Подсчет оставшегося время на тест
	 * @return int
	 */
	static function getTimeRemaining(){
		$remainingTime = 0;
		// проверяем суммарное оставшееся время, для определения конца теста
		foreach ( StackQuestions::getItems() as $item ){
			// если есть вопрос не ограченный по времени
			if( $item["max_time"] == 0 ){
				$remainingTime = $_SESSION["QUESTIONS_MAX_TIME"] - time() + $_SESSION["QUESTIONS_LOAD_TIME"] + 100;
				break;
			}elseif( !empty( $item["START_TIME"] ) && $item["max_time"] - time() - $item["START_TIME"] > 0 ){
				$remainingTime += $item["max_time"] - time() - $item["START_TIME"];
			}
		}
		return $remainingTime > 0 ? $remainingTime : 0;
	}
	/**
	 * Провека на оставшиеся время теста
	 * @return bool
	 */
	static function isEndTimeAll(){
		$isNoLimit = false;
		$remainingTime = 0;
		// проверяем суммарное оставшееся время, для определения конца теста
		foreach ( StackQuestions::getItems() as $item ){
			// если есть вопрос не ограченный по времени
			if( $item["max_time"] == 0 ){
				$isNoLimit = true;
			}elseif( !empty( $item["START_TIME"] ) && $item["max_time"] - time() - $item["START_TIME"] > 0 ){
				$remainingTime += $item["max_time"] - time() - $item["START_TIME"];
			}
		}
		// если есть вопрос без ограничений
		if( $isNoLimit ) {
			$time = $_SESSION["QUESTIONS_MAX_TIME"] - time() + $_SESSION["QUESTIONS_LOAD_TIME"] + 100;
			// если общее время подошло к концу
			if( $time < 1 ){
				return true;
			}
		}elseif( $remainingTime < 1 ){
			return true;
		}

		return false;
	}
}