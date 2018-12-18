<?php

namespace Tester;
/**
 * Класс для работы с вопросом
 * Class Question
 * @package Tester
 */
class Question extends Base
{
	// поля вопроса
	protected $text;
	protected $type;
	protected $max_time;
	// ответ
	public $answer = '';
	// вопрос массив
	private static $question;
	// тип вопроса
	private $ans_type;
	// вопрос
	private $ans;
	// ошибки обработчиков
	private $errors = [];
	// информация по вопросу
	private $info;

	/**
	 * Получение массива ошибок
	 * @return array
	 */
	function getErrors(){
		if( !is_array( $this->errors ) ){
			$this->errors = [];
		}
		return $this->errors;
	}
	/**
	 * Геттер максимального времени на вопрос
	 * @return mixed
	 */
	function getMaxTime(){
		return $this->max_time;
	}
	/**
	 * Получение типа вопроса
	 * @return mixed
	 */
	function getType(){
		return $this->type;
	}
	/**
	 * Получение текста вопроса
	 * @return mixed
	 */
	function getText(){
		return $this->text;
	}
	/**
	 * Геттер вопроса
	 * @return mixed
	 */
	function getAns(){
		return $this->ans;
	}
	/**
	 * Получение типа ответа
	 * @return mixed
	 */
	function getAnsType()
	{
		return $this->ans_type;
	}
	/**
	 * Получаем данные о первом показе вопроса
	 * @return int
	 */
	function getStartTime(){
		$question = StackQuestions::getItem();
		return !empty($question["START_TIME"]) ? $question["START_TIME"] : 0;
	}
	/**
	 * Основоной обработчик ответа на вопрос
	 * Валидация и запись в пулл ответов
	 */
	private function postHandler(){
		if( $_POST ){
			$isLastQuestion = StackQuestions::isLastQuestion() ? true : false;
			// осталось ли время на ответы в тесте
			if( ! StackQuestions::isEndTimeAll() ){
				// осталось ли время на ответ на текущий вопрос
				if( ! StackQuestions::isEndTimeQuestion( StackQuestions::getCurId() ) ) {
					// данные текущего вопроса
					$question = StackQuestions::getItem();
					// для каждого типа вопроса своя обработка
					if (!empty($question["type"])) {
						switch ($question["type"]) {
							// чекбоксы и радиобаттон
							case "vo":
								if (isset($_POST["answer"])) {
									if (is_array($_POST["answer"])) {
										$checkbox_arr = array_map('intval', $_POST["answer"]);
										$_SESSION["ANSWERS"][$_SESSION["QUESTION_ID"]]["val"] = implode(",", $checkbox_arr);
									} else {
										$this->errors[] = "Некорректно заполнены данные в поле с ответом";
									}
								} else {
									$this->errors[] = "Необходимо выбрать вариант ответа";
								}
								break;
							// текстовое поле
							case "open":
								if (isset($_POST["answer"]) && strlen($_POST["answer"]) > 0) {
									if ( $data = htmlspecialchars( strip_tags($_POST["answer"]) ) ) {
										$_SESSION["ANSWERS"][$_SESSION["QUESTION_ID"]]["val"] = $data;
									} else {
										$this->errors[] = "Некорректно заполнены данные в поле с ответом";
									}
								} else {
									$this->errors[] = "Необходимо запонить поле с ответом";
								}
								break;
							// поле с допустимым значение float
							case "int":
								if (isset($_POST["answer"])) {
									if (preg_match("#[0-9\-.]+#", $_POST["answer"])) {
										$_SESSION["ANSWERS"][$_SESSION["QUESTION_ID"]]["val"] = (float)htmlspecialchars(strip_tags($_POST["answer"]));
									} else {
										$this->errors[] = "Некорректно заполнены данные в поле с ответом";
									}
								} else {
									$this->errors[] = "Необходимо запонить поле с ответом";
								}
								break;
							default:
								die("Для заданного типа вопроса не существует обработка");
								break;
						}
						// если вопрос последний и нет ошибок при ответе
						if ($isLastQuestion && empty($this->errors)) {
							$_SESSION["ANSWERS"][$_SESSION["QUESTION_ID"]]["time"] = time() - $_SESSION["QUESTIONS_LIST"][$_SESSION["QUESTION_ID"]]["START_TIME"];
							$_SESSION["END_TIME"] = time();
							// отсыл на результирующую страницу
							header("Location: " . str_replace("index.php", "success.php", $_SERVER["PHP_SELF"]));
						}elseif( empty( $this->errors ) ){
							// установка времени ответа для всех типов впросов одна
							$_SESSION["ANSWERS"][$_SESSION["QUESTION_ID"]]["time"] = time() - $_SESSION["QUESTIONS_LIST"][$_SESSION["QUESTION_ID"]]["START_TIME"];
							// установка следующего вопроса
							StackQuestions::next();
						}
					}else{
						die("Неверный массив ответа");
					}
				} else {
					$this->errors[] = "Время для ответа вышло";
				}
			}else{
				$this->errors[] = "Время теста вышло";
			}
		}
	}

	/**
	 * Question constructor.
	 */
	function __construct( $data = [] ) {

		if( $_POST ){
			$this->postHandler();
		}
		self::$question = StackQuestions::instance($data);
		$this->fieldNeed = [ "text", "type", "max_time"];
		$arField = self::$question->getItem();
		// необходимый набор полей
		parent::__construct($arField);
		$anotherField = [
			"ans_type" , "ans"
		];

		foreach ($anotherField as $item){
			if( !empty($arField[$item]) ){
				$this->{$item} = $arField[$item];
			}
		}
	}
	/**
	 * Получение ответа
	 * @return string
	 */
	function getAnswer(){
		return $this->answer;
	}
	/**
	 * Вывод шаблона, соответствующего типу вопроса
	 */
	function getTemplateAnswer(){

		$dirTemplates = realpath(__DIR__ . DIRECTORY_SEPARATOR. ".." .
			DIRECTORY_SEPARATOR."templates". DIRECTORY_SEPARATOR."form_elements". DIRECTORY_SEPARATOR) ;
		$prefix = "";
		switch ( $this->getType() ){
			case "vo":
				$prefix = mb_strtolower($this->getAnsType());
			case "open":
			case "int":
				$fileDir = $dirTemplates.DIRECTORY_SEPARATOR;
				$fileName = ($prefix) ? $this->getType(). ucfirst($prefix): $this->getType();
				if(file_exists($fileDir.$fileName.".php")){
					include $fileDir.$fileName.".php";
				}else{
					die("Отсутствует шаблон ".$fileName);
				}
				break;
			default:
				die("Заданного типа вопроса не существует обработка");
				break;
		}
	}
	function getInfo(){
		return $this->info;
	}
	/**
	 * Получение ответа на вопрос, если он есть
	 * @return string
	 */
	function getValue(){
		if( isset($_REQUEST["answer"]) ){
			return $_REQUEST["answer"];
		}
		return StackQuestions::getCurValue();
	}
	/**
	 * Получение ответов, для множественного типа
	 * @return array
	 */
	function getValues(){
		if( isset($_REQUEST["answer"]) ){
			return $_REQUEST["answer"];
		}
		$ans = StackQuestions::getCurValue();
		if( $ans !== ""){
			return explode( "," , $ans);
		}
		return [];
	}
	/**
	 * Получение оставшегося времени на ответ
	 * @return int|mixed
	 */
	function getRemainingTime(){

		if( self::getMaxTime() > 0 ){
			$time = self::getMaxTime() - (time() - self::getStartTime());
		}else{
			$time = StackQuestions::getTimeRemaining();
		}
		return $time > 0 ? $time : 0 ;
	}
	/**
	 * Проверка не вышло ли время у вопроса
	 * @return bool
	 */
	function isEndTime(){
		if( $this->getRemainingTime() == 0  ){
			return true;
		}else{
			return false;
		}
	}
	/**
	 * Последний ли вопрос
	 * @return bool
	 */
	function  isLast(){
		return StackQuestions::isLastQuestion();
	}
}