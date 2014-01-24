<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

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
	->_eq('IBLOCK_ID',14)
	->_eq('IBLOCK_SECTION_ID',208);
$arResult['tz']=$obQuery
	->select()
	->all();

$this->IncludeComponentTemplate();