<?php

namespace Tester;

/**
 * Вспомогательный класс для работы вывода
 * Class Helper
 * @package Tester
 */
class Helper
{
	// кол-во пунктов в пагинации
	private static $navCountItems = 0;
	// массив пунктов пагинации
	private static $navItems = [];
	/**
	 * Получаем пункты для пагинации
	 */
	static function initNavItems(){
		if( $items = \Tester\StackQuestions::getItems() ){
			self::$navCountItems = count($items);
		}
		self::$navItems = $items;
	}
	/**
	 * Получаем текущую директорию относительно хоста
	 * @return string
	 */
	static function getCurDur(){
		$urlExplode = explode("/", $_SERVER['REQUEST_URI']);
		unset($urlExplode[0]);
		if( count($urlExplode) > 1 ){
			$urlExplode = array_reverse($urlExplode);
			unset($urlExplode[0]);
			$urlExplode = array_reverse($urlExplode);
		}
		return "/".implode("/",$urlExplode)."/";
	}
	/**
	 * формирует массив с пунктами и подключает шблон пагинации
	 * return void
	 */
	static function showNavString(){
		if( !self::$navCountItems ){
			self::initNavItems();
		}
		$dirTemplates = realpath(__DIR__ . DIRECTORY_SEPARATOR. ".." . DIRECTORY_SEPARATOR."templates". DIRECTORY_SEPARATOR) ;
		if( file_exists($dirTemplates. DIRECTORY_SEPARATOR . "nav.php") )
			include $dirTemplates. DIRECTORY_SEPARATOR . "nav.php";
	}
	/**
	 * формирует строку со временем
	 * @param $seconds - секунды
	 * @return string - форматированная строка
	 */
	static function getHoursMinutes( $seconds ){
		if($seconds){
			$minutes = floor($seconds / 60); // Считаем минуты
			$hours = floor($minutes / 60); // Считаем количество полных часов
			$minutes = $minutes - ($hours * 60);  // Считаем количество оставшихся минут
			return sprintf ("%02d : %02d", $hours, $minutes);
		}else{
			return "";
		}
	}
}