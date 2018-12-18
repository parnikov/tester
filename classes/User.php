<?php
namespace Tester;
/**
 * Класс для работы с пользователем
 * Class User
 * @package Tester
 */
class User extends Base
{
	protected $fam = "";
	protected $im = "";
	protected $otch = "";
	protected $gr = "";
	/**
	 * @return string
	 */
	public function getFam(){
		return $this->fam;
	}
	/**
	 * @return string
	 */
	public function getIm(){
		return $this->im;
	}
	/**
	 * @return string
	 */
	public function getGr(){
		return $this->gr;
	}
	/**
	 * @return string
	 */
	public function getOtch(){
		return $this->otch;
	}
	/**
	 * Получение Ф.И.О
	 * @return string
	 */
	public function getFio(){
		return implode(" ", [ $this->getFam(), $this->getIm(),$this->getOtch() ] );
	}
	/**
	 * Проверка на корректность входных данных по пользователю
	 * User constructor.
	 * @param $arField
	 */
	public function __construct( $arField ) {
		$this->fieldNeed = [ "fam", "im", "otch", "gr"];
		// необходимый набор полей
		parent::__construct($arField);
	}
}