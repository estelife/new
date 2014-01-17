<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

CModule::IncludeModule("iblock");

//Получение последних категория для точки зрения
$obRes = CIBlockSection::GetList(
	array('created'=>'desc'),
	array('ACTIVE'=>'Y', 'IBLOCK_ID'=>14, 'SECTION_ID'=>'208'),
	false,
	array('CODE', 'NAME'),
	array('nPageSize'=>5)
);

while ($res=$obRes->Fetch()){
	$arResult['tz'][] = $res;
}

$this->IncludeComponentTemplate();