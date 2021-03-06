<?php
use core\database\VDatabase;
use core\types\VArray;
use core\types\VString;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
CModule::IncludeModule("iblock");
CModule::IncludeModule("estelife");


$arTabs = array('base', 'reviews', 'prices', 'promotions', 'specialists', 'contacts', 'articles');
$arResult['CURRENT_TAB'] = isset($arParams['CURRENT_TAB']) ? $arParams['CURRENT_TAB'] : 'base';

if (!in_array($arResult['CURRENT_TAB'], $arTabs))
	throw new \core\exceptions\VHttpEx('Invalid request', 404);

$obClinics = VDatabase::driver();
$nClinicID =  (isset($arParams['ID'])) ?
	intval($arParams['ID']) : 0;

//Получаем данные по клинике
$obQuery = $obClinics->createQuery();
$obQuery->builder()->from('estelife_clinics', 'ec');
$obJoin=$obQuery->builder()->join();
$obJoin->_left()
	->_from('ec','city_id')
	->_to('iblock_element','ID','ct')
	->_cond()->_eq('ct.IBLOCK_ID',16);
$obJoin->_left()
	->_from('ec','metro_id')
	->_to('iblock_element','ID','mt')
	->_cond()->_eq('mt.IBLOCK_ID',17);
$obJoin->_left()
	->_from('ec','id')
	->_to('estelife_clinic_contacts','clinic_id','eccp')
	->_cond()->_eq('eccp.type', 'phone');
$obJoin->_left()
	->_from('ec','id')
	->_to('estelife_clinic_contacts','clinic_id','eccw')
	->_cond()->_eq('eccw.type', 'web');
$obQuery->builder()
	->field('ec.*')
	->field('ct.NAME', 'city')
	->field('ct.ID', 'city_id')
	->field('ct.CODE', 'city_code')
	->field('mt.NAME', 'metro')
	->field('eccp.value', 'phone')
	->field('eccw.value', 'web');
$obQuery->builder()->filter()
	->_eq('ec.id', $nClinicID);
$arResult['clinic'] = $obQuery->select()->assoc();

if (empty($arResult['clinic']))
	throw new \core\exceptions\VHttpEx('Invalid request', 404);

if ($arResult['clinic']['clinic_id']>0)
	LocalRedirect('/cl'.$arResult['clinic']['clinic_id'].'/',false,'301 Moved Permanently');
if (!empty($arResult['clinic']['preview_text'])){
	$arResult['clinic']['name'] = $arResult['clinic']['preview_text'];
}


$arResult['clinic']['main_contact'] = array(
	'city' => $arResult['clinic']['city'],
	'city_id' => $arResult['clinic']['city_id'],
	'address' => $arResult['clinic']['address'],
	'metro' => $arResult['clinic']['metro'],
	'phone' => \core\types\VString::formatPhone($arResult['clinic']['phone']),
	'web_short' => \core\types\VString::checkUrl($arResult['clinic']['web']),
	'web'=>$arResult['clinic']['web']
);

//Получаем данные о статье
$obQuery = $obClinics->createQuery();
$obQuery->builder()->from('estelife_clinic_articles', 'eca');
$obJoin=$obQuery->builder()->join();
$obJoin->_left()
	->_from('eca','article_id')
	->_to('iblock_element','ID','ie')
	->_cond()->_eq('ie.IBLOCK_ID',14);
$obJoin->_left()
	->_from('eca','article_id')
	->_to('estelife_likes','element_id','el');
$obJoin->_left()
	->_from('eca','article_id')
	->_to('iblock_element_property','IBLOCK_ELEMENT_ID','iep')
	->_cond()->_eq('iep.IBLOCK_PROPERTY_ID', 151);
$obQuery->builder()
	->field('ie.NAME', 'name')
	->field('ie.ID', 'id')
	->field('ie.PREVIEW_TEXT', 'preview')
	->field('ie.ACTIVE_FROM', 'date')
	->field('el.countLike')
	->field('el.countDislike')
	->field('iep.VALUE', 'value')
	->filter()
	->_eq('eca.clinic_id', $nClinicID);
$arArticles = $obQuery->select()->all();

$arResult['clinic']['articles'] = array();

if (!empty($arArticles)){
	foreach ($arArticles as $val){
		$val['url'] = '/ar'.$val['id'].'/';
		$val['img'] = CFile::GetFileArray($val['value']);
		$val['img']=$val['img']['SRC'];
		$val['preview'] = trim(\core\types\VString::truncate($val['preview'], 80, '...')).'<span></span>';
		$val['date'] = date('d.m.Y',strtotime($val['date']));
		if (empty($val['countLike']))
			$val['countLike'] = 0;
		if (empty($val['countDislike']))
			$val['countDislike'] = 0;
		$arResult['clinic']['articles'][]=$val;
	}
}

//Получаем платежи
$obQuery = $obClinics->createQuery();
$obQuery->builder()->from('estelife_clinic_pays');
$obQuery->builder()->filter()
	->_eq('clinic_id', $nClinicID);
$obQuery->builder()->field('name');
$arPays = $obQuery->select()->all();

if (!empty($arPays)){
	foreach ($arPays as $val){
		$arResult['clinic']['pays'][] = $val['name'];
	}
}

$arResult['clinic']['contacts'][$arResult['clinic']['id']] = array(
	'city' => $arResult['clinic']['city'],
	'address' => $arResult['clinic']['address'],
	'metro' => $arResult['clinic']['metro'],
	'phone' => \core\types\VString::formatPhone($arResult['clinic']['phone']),
	'web_short' => \core\types\VString::checkUrl($arResult['clinic']['web']),
	'web'=> $arResult['clinic']['web'],
	'pays'=> mb_strtolower(implode(', ', $arResult['clinic']['pays']), 'utf-8'),
	'name'=> $arResult['clinic']['name'],
	'lat'=>$arResult['clinic']['latitude'],
	'lng'=>$arResult['clinic']['longitude']
);

//получаем услуги
$obQuery = $obClinics->createQuery();
$obQuery->builder()->from('estelife_clinic_services', 'ecs');
$obJoin=$obQuery->builder()->join();
$obJoin->_left()
	->_from('ecs','specialization_id')
	->_to('estelife_specializations','id','es');
$obJoin->_left()
	->_from('ecs','service_id')
	->_to('estelife_services','id','eser');
$obJoin->_left()
	->_from('ecs','service_concreate_id')
	->_to('estelife_service_concreate','id','econ');
$obQuery->builder()
	->field('es.name','s_name')
	->field('es.id','s_id')
	->field('eser.name','ser_name')
	->field('eser.id','ser_id')
	->field('econ.name','con_name')
	->field('econ.id','con_id')
	->field('ecs.price_from');
$obQuery->builder()->filter()
	->_eq('ecs.clinic_id', $nClinicID);
$arServices = $obQuery->select()->all();

foreach ($arServices as $val){
	$arResult['clinic']['specializations'][$val['s_id']] = $val;
}

foreach ($arServices as $val){
	$arResult['clinic']['service'][$val['ser_id']] = $val;
}

foreach ($arServices as $val){
	$val['price_from']=number_format($val['price_from'],0,'.',' ');
	$arResult['clinic']['con'][$val['con_id']] = $val;
}

foreach ($arResult['clinic']['specializations'] as $val){
	$arResult['clinic']['specializations_string'][] = $val['s_name'];
}

$arResult['clinic']['specializations_string'] = implode(', ', $arResult['clinic']['specializations_string']);
$arResult['clinic']['specializations_string'] = mb_strtolower($arResult['clinic']['specializations_string']);

//Получаем галерею
$obQuery = $obClinics->createQuery();
$obQuery->builder()->from('estelife_clinic_photos');
$obQuery->builder()->filter()
	->_eq('clinic_id', $nClinicID);
$arResult['clinic']['gallery'] = $obQuery->select()->all();


if(!empty($arResult['clinic']['gallery'])){
	foreach($arResult['clinic']['gallery'] as &$arGallery){
		$file=CFile::GetFileArray($arGallery['original']);
		$arGallery['original']=$file['SRC'];
	}
}

//Получаем филиалы
$arClinicIds=array($nClinicID);

$obQuery = $obClinics->createQuery();
$obQuery->builder()->from('estelife_clinics', 'ec');
$obJoin=$obQuery->builder()->join();
$obJoin->_left()
	->_from('ec','city_id')
	->_to('iblock_element','ID','ct')
	->_cond()->_eq('ct.IBLOCK_ID',16);
$obJoin->_left()
	->_from('ec','metro_id')
	->_to('iblock_element','ID','mt')
	->_cond()->_eq('mt.IBLOCK_ID',17);
$obJoin->_left()
	->_from('ec','id')
	->_to('estelife_clinic_contacts','clinic_id','eccp')
	->_cond()->_eq('eccp.type', 'phone');
$obJoin->_left()
	->_from('ec','id')
	->_to('estelife_clinic_contacts','clinic_id','eccw')
	->_cond()->_eq('eccw.type', 'web');
$obQuery->builder()
	->field('ec.address', 'address')
	->field('ec.longitude', 'lng')
	->field('ec.latitude', 'lat')
	->field('ct.NAME', 'city')
	->field('mt.NAME', 'metro')
	->field('eccp.value', 'phone')
	->field('eccw.value', 'web')
	->field('ec.id', 'id')
	->field('ec.name', 'name');

$obQuery->builder()->filter()
	->_eq('ec.clinic_id', $nClinicID);

$arResult['filial'] = $obQuery->select()->all();

foreach ($arResult['filial'] as $val){
	$arFilials[] = $val['id'];

	if(!empty($val['phone']))
		$val['phone']=\core\types\VString::formatPhone($val['phone']);

	if(!empty($val['web']))
		$val['web_short']=\core\types\VString::checkUrl($val['web']);

	$arClinicIds[]=$val['id'];
	$arResult['clinic']['contacts'][$val['id']] = $val;
}


if (!empty($arFilials)){
	$obQuery = $obClinics->createQuery();
	$obQuery->builder()->from('estelife_clinic_pays');
	$obQuery->builder()->filter()
		->_in('clinic_id', $arFilials);
	$arFilialsPays= $obQuery->select()->all();
}

$arPays = array();

if (!empty($arFilialsPays)){
	foreach ($arFilialsPays as $val){
		$arPays[$val['clinic_id']][] = $val['name'];
	}

	foreach ($arPays as $key=>$val){
		$arResult['clinic']['contacts'][$key]['pays'] =  mb_strtolower(implode(', ', $val), 'utf-8');
	}
}

//Получаем специалистов
$obQuery = $obClinics->createQuery();
$obQuery->builder()->from('estelife_professionals_clinics', 'epc');
$obJoin = $obQuery->builder()->join();
$obJoin->_left()
	->_from('epc', 'professional_id')
	->_to('estelife_professionals', 'id', 'ep');
$obJoin->_left()
	->_from('ep', 'user_id')
	->_to('user', 'ID', 'u');
$obQuery->builder()
	->field('ep.id','id')
	->field('u.NAME','name')
	->field('u.LAST_NAME', 'last_name')
	->field('u.SECOND_NAME', 'second_name')
	->field('ep.image_id', 'image_id')
	->field('ep.short_description', 'short_description')
	->filter()
	->_eq('epc.clinic_id', $nClinicID);
$arProfessionals = $obQuery->select()->all();
if (!empty($arProfessionals)){
	foreach ($arProfessionals as $arData){
		$arData['link']='/pf'.$arData['id'].'/';
		if (empty($arData['last_name']))
			$arData['name']=VString::brForName($arData['name']);
		else
			$arData['name']=VString::brForName($arData['last_name'].' '.$arData['name'].' '.$arData['second_name']);

		$arData['short_description'] = \core\types\VString::truncate(html_entity_decode($arData['short_description'],ENT_QUOTES), 120, '...');

		if(!empty($arData['image_id'])){
			$file=CFile::ShowImage($arData["image_id"], 227, 158,'alt="'.$arData['name'].'"');
			$arData['logo']=$file;
		}
		$arResult['clinic']['professionals'][]=$arData;
	}
}

//Получаем акции
$obQuery = $obClinics->createQuery();
$obJoin=$obQuery
	->builder()
	->from('estelife_clinic_akzii', 'ecs')
	->group('ecs.akzii_id')
	->join();
$obJoin->_left()
	->_from('ecs','akzii_id')
	->_to('estelife_akzii','id','ea');

$obQuery->builder()
	->field('ea.id','id')
	->field('ea.name','name')
	->field('ea.end_date','end_date')
	->field('ea.base_old_price','old_price')
	->field('ea.base_new_price','new_price')
	->field('ea.base_sale','sale')
	->field('ea.small_photo','logo_id')
	->field('ea.view_type','view_type');
$obQuery->builder()->filter()
	->_in('ecs.clinic_id', $arClinicIds)
	->_eq('ea.active', 1)
	->_gte('ea.end_date', time());

$arActions = $obQuery->select()->all();

$arNow = time();
foreach ($arActions as $val){
	$val['time'] = ceil(($val['end_date']-$arNow)/(60*60*24));
	$val['day'] = \core\types\VString::spellAmount($val['time'], 'день,дня,дней');
	$val['link'] = '/pr'.$val['id'].'/';
	$val['new_price']=number_format($val['new_price'],0,'.',' ');
	$val['old_price']=number_format($val['old_price'],0,'.',' ');

	if(!empty($val['logo_id'])){
		$file=CFile::GetFileArray($val["logo_id"]);
		$val['logo']=$file['SRC'];
	}

	$arResult['clinic']['akzii'][]=$val;
}

if(!empty($arResult['clinic']['logo_id']))
	$arResult['clinic']['logo']=CFile::ShowImage($arResult['clinic']['logo_id'],200,85);

$arResult['clinic']['detail_text']=htmlspecialchars_decode($arResult['clinic']['detail_text'],ENT_NOQUOTES);

$arResult['clinic']['city_name']=$arCity = $arResult['clinic']['city'];
if (!empty($arCity))
	$arCity = $arCity;
else
	$arCity = '';

$arResult['clinic']['name'] = trim(strip_tags(html_entity_decode($arResult['clinic']['name'], ENT_QUOTES, 'utf-8')));
$arResult['clinic']['seo_name'] = \core\types\VString::pregStrSeo($arResult['clinic']['name']);

$sPrefix='Клиника ';

if(preg_match('#(клиник)#ui',$arResult['clinic']['seo_name']))
	$sPrefix='';


$nCityId = 0;

if(isset($arResult['clinic']['city_id'])&& !empty($arResult['clinic']['city_id'])){
	$nCityId = $arResult['clinic']['city_id'];
}

if($nCityId>0){
	//Получаем имя города по его ID
	$obCity=CIBlockElement::GetList(
		array("NAME"=>"ASC"),
		array("IBLOCK_ID"=>16, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "ID" => $nCityId),
		false,
		false,
		array("ID", "NAME", 'PROPERTY_CITY')
	);
	$arResult['city']=$obCity->Fetch();
	$arResult['city']['R_NAME']=(!empty($arResult['city']['PROPERTY_CITY_VALUE'])) ?
		$arResult['city']['PROPERTY_CITY_VALUE'] :
		$arResult['city']['NAME'];
}

if(isset($arResult['city'])&& !empty($arResult['city'])){
	$arCity = $arResult['city']['NAME'];
}

if($arResult['CURRENT_TAB']=='prices'){
	$arResult['clinic']['seo_title'] = $sPrefix.$arResult['clinic']['seo_name'].' '.$arCity.' - цены и услуги';
	$arResult['clinic']['seo_description'] = $sPrefix.$arResult['clinic']['seo_name'].' '.$arCity.' -  продробная информация о ценах и услугах здесь.';
}else if($arResult['CURRENT_TAB']=='promotions'){
	$arResult['clinic']['seo_title'] = $sPrefix.$arResult['clinic']['seo_name'].' '.$arCity.' - акции и скидки';
	$arResult['clinic']['seo_description'] = $sPrefix.$arResult['clinic']['seo_name'].' '.$arCity.' -  вся информация о проводимых акциях и предоставляемых скидках. Читайте.';
}else if($arResult['CURRENT_TAB']=='reviews'){
	$arResult['clinic']['seo_title'] = $sPrefix.$arResult['clinic']['seo_name'].' '.$arCity.' - отзывы пациентов';
	$arResult['clinic']['seo_description'] = $sPrefix.$arResult['clinic']['seo_name'].' '.$arCity.' -  читайте отзывы пациентов у нас.';
}else if($arResult['CURRENT_TAB']=='contacts'){
	$arResult['clinic']['seo_title'] = $sPrefix.$arResult['clinic']['seo_name'].' '.$arCity.' - адреса, карты и контакты';
	$arResult['clinic']['seo_description'] = $sPrefix.$arResult['clinic']['seo_name'].' '.$arCity.' -  все адреса и  месторасположение на карте, а так же контакты. Смотрите здесь.';
}else if($arResult['CURRENT_TAB']=='articles'){
	$arResult['clinic']['seo_title'] = $sPrefix.$arResult['clinic']['seo_name'].' '.$arCity.' - мнение экспертов и статьи';
	$arResult['clinic']['seo_description'] = $sPrefix.$arResult['clinic']['seo_name'].' '.$arCity.' -  экслюзивно на портале Estelife предлагает вашему вниманию интересные статьи и мнения своих специалистов. Только лучшее.';
}else if($arResult['CURRENT_TAB']=='professionals'){
	$arResult['clinic']['seo_title'] = $sPrefix.$arResult['clinic']['seo_name'].' '.$arCity.' - врачи и специалисты клиники';
	$arResult['clinic']['seo_description'] = $sPrefix.$arResult['clinic']['seo_name'].' '.$arCity.' -  Название клиники - полный список врачей и специалистов с подробной информацией. Читайте у нас.';
}else{
	$arResult['clinic']['seo_title'] = $sPrefix.$arResult['clinic']['seo_name'].' '.$arCity.' - акции, цены, адреса';
	$arResult['clinic']['seo_description'] = $sPrefix.$arResult['clinic']['seo_name'].' '.$arCity.' - подробная информация, адреса, контакты и акции.';
}

$APPLICATION->SetPageProperty("title", $arResult['clinic']['seo_title']);
$APPLICATION->SetPageProperty("description", $arResult['clinic']['seo_description']);

$this->IncludeComponentTemplate();