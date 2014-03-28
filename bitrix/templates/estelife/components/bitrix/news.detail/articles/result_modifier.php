<?
use core\database\VDatabase;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

global $APPLICATION;
CModule::IncludeModule("estelife");

if (!empty($arResult['SECTION']['PATH']))
	$arResult['LAST_SECTION'] = array_pop($arResult['SECTION']['PATH']);


$isShort =  preg_match_all('[(.*)]',$arResult['DETAIL_TEXT'],$arMathes);

$arUsers = array();

foreach($arMathes as $key=>$value){
	foreach($value as $val){

		$isShort =  stristr($val,'EXPERT');

		if(!empty($isShort)){

			$sSpec = str_replace(']','',$isShort);
			$nSpec = str_replace('EXPERT_','',$sSpec);
			$nSpec = intval($nSpec);

			array_push($arUsers,$nSpec);
		}
	}
}

$obDriver = VDatabase::driver();


$obQuery = $obDriver->createQuery();
$obQuery->builder()
	->from('user', 'u');
$obJoin=$obQuery->builder()->join();
$obJoin->_left()
	->_from('u', 'ID')
	->_to('estelife_professionals', 'user_id', 'ep');
$obJoin->_left()
	->_from('ep', 'image_id')
	->_to('file', 'ID', 'fl');
$obQuery->builder()
	->field('u.ID')
	->field('ep.id','user_id')
	->field('u.NAME', 'user_name')
	->field('u.LAST_NAME', 'last_name')
	->field('u.SECOND_NAME', 'second_name')
	->field(
		$obQuery->builder()->_concat('fl.SUBDIR', '/', 'fl.FILE_NAME'),
		'image'
	)
	->field('ep.short_description','short_description');
$obQuery->builder()->filter()
	->_in('ep.id', $arUsers);

$arSpecialists = $obQuery->select()->all();




foreach($arMathes as $key=>$value){
	foreach($value as $val){

		$isShort =  stristr($val,'EXPERT');

		if(!empty($isShort)){

			$sSpec = str_replace(']','',$isShort);
			$nSpec = str_replace('EXPERT_','',$sSpec);
			$nSpec = intval($nSpec);
			$sArText = explode(":", $isShort);
			$sTextAr = explode(']',$sArText[1]);
			$sText = trim($sTextAr[0]);

			$arSpec = array();


			foreach($arSpecialists as $key=>$value){
				if($value['user_id'] == $nSpec){
					$arSpec = $arSpecialists[$key];
				}
			}

			if(!empty($arSpec)){
				$sSpecName = ''.$arSpec['last_name'].' '.$arSpec['user_name'].' '.$arSpec['second_name'];
				$sShortDesription = $arSpec['short_description'];

				$html = '<div class="specialist">
							<h2>Комментарий эксперта<i></i></h2>
							<div class="about">
								<a href="/pf'.$nSpec.'"><img src="/upload/'.$arSpec['image'].'" alt="'.$sSpecName.'" title="'.$sSpecName.'" /></a>
								<b>'.$sSpecName.'</b>
								<i>'.$sShortDescription.'</i>
							</div>
							<p>'.$sText.'</p>
						</div>';

				$sShort = '[EXPERT_'.$nSpec.' : '.$sText.']';

				$arResult['DETAIL_TEXT'] = str_replace($sShort,$html,$arResult['DETAIL_TEXT']);
			}

		}
	}
}



$arResult['LAST_SECTION']['SECTION_PAGE_URL'] = preg_replace('/stati/', $arParams['SECTION_CODE'], $arResult['LAST_SECTION']['SECTION_PAGE_URL']);
$arResult['IMG']=CFile::GetFileArray($arResult['PROPERTIES']['INSIDE']['VALUE']);

$APPLICATION->AddHeadString('<meta name="og:title" content="'.$arResult["NAME"].'" />');
$APPLICATION->AddHeadString('<meta name="og:description" content="'.$arResult["PREVIEW_TEXT"].'" />');
$APPLICATION->AddHeadString('<meta name="og:image" content="http://estelife.ru'.$arResult['IMG']['SRC'].'" />');

if ($arResult['ID']>0){
	$obLikes=new \like\VLike(\like\VLike::ARTICLE);
	$arResult['LIKES']=$obLikes->getLikes($arResult['ID']);
}

if(!empty($_GET['utm_source']) && $_GET['utm_source']=='arc'){
	$arAvNums = array(2,4,6,8);
	$arResult['utm'] = array();

	for($i=0,$c=count($arAvNums); $i<$c; $i++) {
		$nKey = array_rand($arAvNums,1);
		$arResult['utm'][] = $arAvNums[$nKey];
	}

	$arResult['utm'] = implode('', $arResult['utm']);
}
$arTypes=array_values($APPLICATION->IncludeComponent(
	'estelife:system-settings',
	'',
	array('filter'=>'types')
));
$arResult['INT_TYPES'] = array_flip($arTypes);

$arIblockIds = array(
	14 => 'ar',
	36 => 'pt',
	3 => 'ns'
);
$arResult['TYPE']=$arIblockIds[$arResult['IBLOCK_ID']];