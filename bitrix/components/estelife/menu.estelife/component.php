<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

CModule::IncludeModule("estelife");
$obQuery=\core\database\VDatabase::driver()->createQuery();
$obQuery->builder()
	->from('iblock_section','section')
	->field('section.CODE','CODE')
	->field('section.NAME','NAME')
	->slice(0,5)
	->sort('section_prop.UF_DATE_PUB_SECTION','DESC')
	->filter()
	->_eq('section.ACTIVE','Y')
	->_eq('section.IBLOCK_ID',36)
	->_lte('section_prop.UF_DATE_PUB_SECTION',date('Y-m-d 00:00:00'));
$obQuery->builder()
	->join()
	->_left()
	->_from('section','ID')
	->_to('uts_iblock_36_section','VALUE_ID','section_prop');
$arResult['tz']=$obQuery
	->select()
	->all();

//костыль для Ивора
if (preg_match('#^\/(yvoire).*#',$_SERVER['REQUEST_URI'])){
	$arResult['yvoire']=1;
}

$this->IncludeComponentTemplate();