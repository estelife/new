<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$sPath=GetPagePath();
$arPath=explode('/',$sPath);
$arPath=array_values(array_diff($arPath,array('')));

$sRedirect=false;
$arAssoc=$APPLICATION->IncludeComponent(
	'estelife:system-settings',
	'',
	array('filter'=>'directions')
);
$arAssoc=array_flip($arAssoc);

switch($arPath[0]){
	case 'clinic':
	case 'promotions':
	case 'sponsors':
	case 'training-centers':
		if(preg_match('#([0-9]+)/?$#',$arPath[1],$arMatches))
			$sRedirect='/'.$arAssoc[$arPath[0]].$arMatches[1].'/';
		break;
	case 'novosti':
	case 'articles':
	case 'podcast':
		CModule::IncludeModule('iblock');
		$sPath=($arPath[0]=='novosti') ?
			$arPath[2] : $arPath[1];

		if(preg_match('#([a-z0-9\-]+\-[0-9]{6,8})#',$sPath,$arMatches)){
			$obElements=CIBlockElement::GetList(false,array(
				'CODE'=>$arMatches[1],
				'IBLOCK_ID'=>($arPath[0]=='novosti') ? 3 : 14
			),false,array('nPageSize'=>1),array('ID'));

			if($arElement=$obElements->Fetch())
				$sRedirect='/'.$arAssoc[$arPath[0]].$arElement['ID'].'/';
		}
		break;
	case 'apparatuses-makers':
	case 'preparations-makers':
	case 'preparations':
	case 'apparatuses':
	case 'events':
	case 'trainings':
		CModule::IncludeModule('estelife');
		$obQuery=\core\database\VDatabase::driver()->createQuery();

		if($arPath[0]=='apparatuses-makers' || $arPath[0]=='preparations-makers')
			$obQuery->builder()->from('estelife_companies');
		else if($arPath[0]=='preparations')
			$obQuery->builder()->from('estelife_pills');
		else if($arPath[0]=='apparatuses')
			$obQuery->builder()->from('estelife_apparatus');
		else
			$obQuery->builder()->from('estelife_events');

		$obQuery->builder()
			->slice(0,1)
			->field('id')
			->filter()
			->_eq('translit',$arPath[1]);
		$arRecord=$obQuery->select()->assoc();

		if(!empty($arRecord))
			$sRedirect='/'.$arAssoc[$arPath[0]].$arRecord['id'].'/';
		break;
}

if(!$sRedirect)
	LocalRedirect('/'.$arPath[0].'/',false,'301 Moved Permanently');
else
	LocalRedirect($sRedirect,false,'301 Moved Permanently');


require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");