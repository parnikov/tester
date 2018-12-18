<?php

namespace Tester;
/**
 * Класс - родитель функционал - валидация
 * Class Base
 * @package Tester
 */
abstract class Base
{
	protected $fieldNeed = [];

	public function __construct($arField)
	{
		if( !empty($this->fieldNeed) ){
			if( !empty( $arField ) && is_array($arField) ){
				$arrEmpty = array_diff_key( array_flip($this->fieldNeed), $arField );

				if( empty($arrEmpty) ){
					if( !in_array("", $arField) ){
						foreach ( $this->fieldNeed as $field){

							$this->{$field} = $arField[$field];
						}
					}else{
						die("В массиве пользователя не заполнено поле: ".array_search("", $arField));
					}
				}else{
					die("Массив информации неполон, отсутствуют ключи: ".implode(", ", array_keys($arrEmpty)));
				}
			}else{
				die("Неверные данные для формирования массива информации");
			}
		}else{
			die("Не определены обязательные поля в классе");
		}
	}
}
