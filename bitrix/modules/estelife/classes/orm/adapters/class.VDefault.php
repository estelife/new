<?php
namespace lists\adapters;
use core\types\VString;

/**
 * Вероятно я забыл оставить описание файла. Обратитесь на мыло за уточнениями.
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 * @since 03.12.13
 */
class VDefault implements VAdapter {
	public function prepare(&$arData){
		$arData['name']=trim($arData['name']);
		$arData['link'] = '/clinic/'.VString::translit($arData['name']).'-'.$arData['id'].'/';

		if(!empty($arData['logo_id'])){
			$file=\CFile::ShowImage($arData["logo_id"],110,90,'alt="'.$arData['name'].'"');
			$arData['logo']=$file;
		}

		if(!empty($arData['phone']))
			$arData['phone']=VString::formatPhone($arData['phone']);

		if(!empty($arData['web']))
			$arData['web_short']=VString::checkUrl($arData['web']);

		return $arData;
	}
}