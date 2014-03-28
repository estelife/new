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
	case 'actions':
	case 'organizatory':
	case 'sponsors':
	case 'uchebnie-centry':
	case 'organizers':
	case 'training-centers':
		if($arPath[0]=='uchebnie-centry')
			$arPath[0]='training-centers';
		else if ($arPath[0]=='sponsors')
			$arPath[0]='organizers';
		else if($arPath[0]=='clinic')
			$arPath[0]='clinics';
		else if($arPath[0]=='organizatory')
			$arPath[0]='sponsors';
		else if($arPath[0]=='actions')
			$arPath[0]='promotions';

		if(preg_match('#([0-9]+)/?$#',$arPath[1],$arMatches))
			$sRedirect='/'.$arAssoc[$arPath[0]].$arMatches[1].'/';
		else
			$sRedirect='/'.$arPath[0].'/';
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
				'IBLOCK_ID'=>($arPath[0]=='novosti') ? 3 : 14,
				'ACTIVE_DATE'=>'Y',
				'ACTIVE'=>'Y'
			),false,array('nPageSize'=>1),array('ID'));

			if($arElement=$obElements->Fetch())
				$sRedirect='/'.$arAssoc[$arPath[0]].$arElement['ID'].'/';
		}

		if(!$sRedirect)
			$sRedirect='/';
		break;
	case 'maker-apparatus':
	case 'apparatuses-makers':
	case 'preparations-makers':
	case 'maker-pills':
	case 'preparations':
	case 'apparatuses':
	case 'apparatus':
	case 'pills':
	case 'events':
	case 'trainings':
	case 'training':
		if($arPath[0]=='apparatus')
			$arPath[0]='apparatuses';
		else if($arPath[0]=='pills')
			$arPath[0]='preparations';
		else if($arPath[0]=='training')
			$arPath[0]='trainings';
		else if($arPath[0]=='maker-pills')
			$arPath[0]='preparations-makers';
		else if($arPath[0]=='maker-apparatus')
			$arPath[0]='apparatuses-makers';

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
		else{
			$sRedirect='/'.$arPath[0].'/';
		}
		break;
	case 'pr':
		$sRedirect='/promotions/';
		break;
	default:
		$sRedirect='/';
		break;
}

if(!$sRedirect)
	LocalRedirect('/'.$arPath[0].'/',false,'301 Moved Permanently');
else
	LocalRedirect($sRedirect,false,'301 Moved Permanently');


require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");