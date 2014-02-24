<?
use core\database\VDatabase;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

global $APPLICATION;
CModule::IncludeModule("estelife");

$arResult['IMG']=CFile::GetFileArray($arResult['PROPERTIES']['AUTOR_IMG_BIG']['VALUE']);
$arResult['AUTOR'] = $arResult['PROPERTIES']['AUTOR']['VALUE'];
$arResult['PROFESSION'] = $arResult['PROPERTIES']['PROFESSION']['VALUE'];
$arResult['FIO'] = $arResult['PROPERTIES']['FIO']['VALUE'];
$arResult['ABOUT'] = $arResult['PROPERTIES']['ABOUT']['VALUE']['TEXT'];
$arResult['THEME'] = $arResult['PROPERTIES']['THEME']['VALUE'];

unset(
$arResult['PROPERTIES']['AUTOR'],
$arResult['PROPERTIES']['PROFESSION'],
$arResult['PROPERTIES']['AUTOR_IMG_BIG'],
$arResult['PROPERTIES']['FIO'],
$arResult['PROPERTIES']['ABOUT']
);

if ($arResult['ID']>0){
	$obLikes=new \like\VLike(\like\VLike::EXPERT);
	$arResult['LIKES']=$obLikes->getLikes($arResult['ID']);
}

$arResult['TYPE']='ex';