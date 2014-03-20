<?php
/**
 * Created by JetBrains PhpStorm.
 * User: shlemarev
 * Date: 04.03.14
 * Time: 12:39
 * To change this template use File | Settings | File Templates.
 */


// регистрируем обработчик
AddEventHandler("iblock", "OnAfterIBlockElementAdd", Array("YandexPost", "OnAfterIBlockElementAddHandler"));

class YandexPost
{
	// создаем обработчик события "OnAfterIBlockElementAdd"
	function OnAfterIBlockElementAddHandler(&$arFields)
	{
		CModule::IncludeModule("estelife");
		CModule::IncludeModule('iblock');

		$arBlocks = array(
			14=>14,
			36=>36,
			35=>36,
			3=>3,
		);

		if($arFields["ID"]>0 && array_key_exists($arFields['IBLOCK_ID'],$arBlocks)){

			$obQuery = \core\database\VDatabase::driver()->createQuery();
			$obQuery->builder()
				->from('estelife_yandex_content')
				->value('iblock_element', intval($arFields["ID"]))
				->value('send', intval(0));

			$obQuery->insert();
		}

	}


}



