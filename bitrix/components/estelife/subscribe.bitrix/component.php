<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if(!IsModuleInstalled("subscribe"))
{
	ShowError(GetMessage("SUBSCR_MODULE_NOT_INSTALLED"));
	return;
}
CModule::IncludeModule("subscribe");
$obSubscription = new CSubscription;

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 3600;
if($arParams["CACHE_TYPE"] == "N" || ($arParams["CACHE_TYPE"] == "A" && COption::GetOptionString("main", "component_cache_on", "Y") == "N"))
	$arParams["CACHE_TIME"] = 0;
	

if(!CModule::IncludeModule("subscribe"))
{
	ShowError(GetMessage("SUBSCR_MODULE_NOT_INSTALLED"));
	return;
}
//Получение подписки авторизованного пользователя
if (!$USER->IsAuthorized()){
	$arEmail = $arParams['EMAIL'];
}else{
	$arEmail = $USER->GetEmail();
}


$arSubscription = CSubscription::GetByEmail($arEmail)->Fetch();
$arSubID = $arSubscription['ID'];
//Получение массива рассылок
$arResult['sub'] = CSubscription::GetRubricArray($arSubID);


//Обработка запроса на подписку
if($arParams['ACTION'] == 'update'){

	if (!empty($arParams["RUB_ID"])){
		$arRUB_ID = explode('_', $arParams["RUB_ID"]);
	}
	
	$arNewRubrics = array();
	if(is_array($arRUB_ID))
	{
		foreach($arRUB_ID as $rub_id)
		{
			$rub_id = intval($rub_id);
			if($rub_id > 0)
				$arNewRubrics[$rub_id] = $rub_id;
		}
	}

	//удаляем подписку если она есть, и мы ничего не выбрали
	if (count($arNewRubrics) <= 0){
		if (!empty($arSubscription)){
			$rs = $obSubscription->Delete($arSubscription["ID"]);
			if(!$rs){
				$arResult["ERRORS"][] = GetMessage("CC_BSS_DELETE_ERROR");
				$arResult["subscribe"][] = $obSubscription->LAST_ERROR;
			}else{
				$_SESSION["subscribe.simple.message"] = GetMessage("CC_BSS_UPDATE_SUCCESS");
				$arResult["subscribe"] = 'subscribe_delete';
			}
		}
	}else{
	//Добавляем новые рассылки на подписку
		if (!empty($arSubscription)){
			$rs = $obSubscription->Update(
				$arSubscription["ID"],
				array(
					"FORMAT" => "html",
					"RUB_ID" => $arNewRubrics,
				),
				false
			);

			if(!$rs){
				$arResult["ERRORS"][] = $obSubscription->LAST_ERROR;
				$arResult["subscribe"][] = $obSubscription->LAST_ERROR;
			}else{
				$_SESSION["subscribe.simple.message"] = GetMessage("CC_BSS_UPDATE_SUCCESS");
				$arResult["subscribe"] = 'subscribe_update';
			}
		}else{
			$ID = $obSubscription->Add(array(
				"USER_ID" => ($USER->IsAuthorized()? $USER->GetID():false),
				"ACTIVE" => "Y",
				"EMAIL" => $arEmail,
				"FORMAT" => "html",
				"CONFIRMED" => "Y",
				"SEND_CONFIRM" => "N",
				"RUB_ID" => $arNewRubrics,
			));

			if(!$ID){
				$arResult["ERRORS"][] = $obSubscription->LAST_ERROR;
				$arResult["subscribe"][] = $obSubscription->LAST_ERROR;
			}else{
				$_SESSION["subscribe.simple.message"] = GetMessage("CC_BSS_UPDATE_SUCCESS");
				$arResult["subscribe"] = 'subscribe_insert';
			}
		}
	}

//	if(count($arResult["ERRORS"]) <= 0)
//	{
//		LocalRedirect($APPLICATION->GetCurPageParam());
//	}
}

//Получение списка подписок
$obCache = new CPHPCache;
$strCacheID = LANG.$arParams["SHOW_HIDDEN"];
if($obCache->StartDataCache($arParams["CACHE_TIME"], $strCacheID, "/".SITE_ID.$this->GetRelativePath()))
{
	if(!CModule::IncludeModule("subscribe"))
	{
		$obCache->AbortDataCache();
		ShowError(GetMessage("SUBSCR_MODULE_NOT_INSTALLED"));
		return;
	}

	$arFilter = array("ACTIVE"=>"Y", "LID"=>LANG);
	if(!$arParams["SHOW_HIDDEN"])
		$arFilter["VISIBLE"]="Y";
	$rsRubric = CRubric::GetList(array("SORT"=>"ASC", "NAME"=>"ASC"), $arFilter);
	$arRubrics = array();
	while($arRubric = $rsRubric->GetNext())
	{
		$arRubrics[]=$arRubric;
	}
	$obCache->EndDataCache($arRubrics);
}
else
{
	$arRubrics = $obCache->GetVars();
}

if(count($arRubrics)<=0)
{
	ShowError(GetMessage("SUBSCR_NO_RUBRIC_FOUND"));
	return;
}
$arResult["RUBRICS"] = $arRubrics;

$this->IncludeComponentTemplate();