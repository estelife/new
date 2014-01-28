<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

/*CModule::IncludeModule("iblock");

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
}*/

CModule::IncludeModule("estelife");
$obQuery=\core\database\VDatabase::driver()->createQuery();
$obQuery->builder()
	->from('iblock_section')
	->field('CODE')
	->field('NAME')
	->slice(0,5)
	->sort('DATE_CREATE','DESC')
	->filter()
	->_eq('ACTIVE','Y')
	->_eq('IBLOCK_ID',37);
$arResult['tz']=$obQuery
	->select()
	->all();

$this->IncludeComponentTemplate();