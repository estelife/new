<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if($arParams["USE_FILTER"]=="Y")
{
	if(strlen($arParams["FILTER_NAME"])<=0 || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"]))
		$arParams["FILTER_NAME"] = "arrFilter";
}
else
	$arParams["FILTER_NAME"] = "";

$arParams["USE_CATEGORIES"]=$arParams["USE_CATEGORIES"]=="Y";

if($arParams["USE_CATEGORIES"])
{
	if(!is_array($arParams["CATEGORY_IBLOCK"]))
		$arParams["CATEGORY_IBLOCK"] = array();
	$ar = array();
	foreach($arParams["CATEGORY_IBLOCK"] as $key=>$value)
	{
		$value=intval($value);
		if($value>0)
			$ar[$value]=true;
	}
	$arParams["CATEGORY_IBLOCK"] = array_keys($ar);
}

$arParams["CATEGORY_CODE"]=trim($arParams["CATEGORY_CODE"]);
if(strlen($arParams["CATEGORY_CODE"])<=0)
	$arParams["CATEGORY_CODE"]="CATEGORY";
$arParams["CATEGORY_ITEMS_COUNT"]=intval($arParams["CATEGORY_ITEMS_COUNT"]);
if($arParams["CATEGORY_ITEMS_COUNT"]<=0)
	$arParams["CATEGORY_ITEMS_COUNT"]=5;

if(!is_array($arParams["CATEGORY_IBLOCK"]))
	$arParams["CATEGORY_IBLOCK"] = array();
foreach($arParams["CATEGORY_IBLOCK"] as $iblock_id)
	if($arParams["CATEGORY_THEME_".$iblock_id]!="photo")
		$arParams["CATEGORY_THEME_".$iblock_id]="list";

$arDefaultUrlTemplates404 = array(
	"news" => "",
	"search" => "search/",
	"rss" => "rss/",
	"rss_section" => "#CURRENT_CODE#/rss/",
	"current" => "#CURRENT_CODE#/",
	"section" => "",
);
$arDefaultVariableAliases404 = array();
$arDefaultVariableAliases = array();
$arComponentVariables = array(
	"CURRENT_CODE",
	"CURRENT_ELL"
);
$arParams["SEF_URL_TEMPLATES"]=Array(
	"section" => "#SECTION_CODE#/",
	"detail" => "#ELEMENT_CODE#/",
	"index" => ""
);

$arVariables = array();

$arUrlTemplates = CComponentEngine::MakeComponentUrlTemplates($arDefaultUrlTemplates404, $arParams["SEF_URL_TEMPLATES"]);
$arVariableAliases = CComponentEngine::MakeComponentVariableAliases($arDefaultVariableAliases404, $arParams["VARIABLE_ALIASES"]);

$engine = new CComponentEngine($this);
if (CModule::IncludeModule('iblock'))
{
	$engine->addGreedyPart("#SECTION_CODE_PATH#");
	$engine->setResolveCallback(array("CIBlockFindTools", "resolveComponentEngine"));
}
$componentPage = $engine->guessComponentPath(
	$arParams["SEF_FOLDER"],
	$arUrlTemplates,
	$arVariables
);

$sCode=htmlspecialchars($arVariables['CURRENT_CODE'],ENT_QUOTES,'utf-8');

if (empty($sCode)){
	$componentPage='index';
	$arVariables['SECTION_CODE']=false;
}elseif ($sCode == 'about'){
	$componentPage='about';
	$arVariables['SECTION_CODE']=false;
}elseif ($sCode == 'opinions'){
	$componentPage='opinions';
	$arVariables['SECTION_CODE']=false;
}elseif ($sCode == 'products'){
	$componentPage='products';
	$arVariables['SECTION_CODE']=false;
}elseif ($sCode == 'events'){
	$componentPage='events';
	$arVariables['SECTION_CODE']=false;
}else{
	$obQuery=\core\database\VDatabase::driver(\core\database\mysql\VDriver::MYSQL)->createQuery();

	$obIf=$obQuery->builder()->_if();
	$obIf->when(
		'section',
		'detail'
	)->_eq(
		'isection.CODE',
		$sCode
	);
	$obFilter=$obQuery->builder()
		->from('iblock_section','isection')
		->from('iblock_element','ielement')
		->field(
			$obIf,
			'type'
		)
		->field('isection.ID','is_id')
		->field('ielement.ID','ie_id')
		->slice(0,1)
		->filter();
	$obFilter->_or()
		->_eq('isection.CODE',$sCode)
		->_eq('isection.IBLOCK_ID',intval($arParams['IBLOCK_ID']));
	$obFilter->_or()
		->_eq('ielement.CODE',$sCode)
		->_eq('ielement.IBLOCK_ID',intval($arParams['IBLOCK_ID']));

	$arResult=$obQuery->select()->assoc();

	if(!empty($arResult) && !empty($arVariables)){
		$componentPage=$arResult['type'];
		$sKey=($componentPage=='section') ?
			'SECTION_CODE' : 'ELEMENT_CODE';

		$arVariables[$sKey]=$sCode;

	}else{
		$componentPage=false;
		$arVariables['SECTION_CODE']=false;
	}
}

$b404 = false;

if(!$componentPage)
{
	$componentPage = "404";
	$b404 = true;
}

if($b404 && $arParams["SET_STATUS_404"]==="Y")
{
	$folder404 = str_replace("\\", "/", $arParams["SEF_FOLDER"]);

	if ($folder404 != "/")
		$folder404 = "/".trim($folder404, "/ \t\n\r\0\x0B")."/";

	if (substr($folder404, -1) == "/")
		$folder404 .= "index.php";

	if($folder404 != $APPLICATION->GetCurPage(true))
		CHTTP::SetStatus("404 Not Found");
}

CComponentEngine::InitComponentVariables($componentPage, $arComponentVariables, $arVariableAliases, $arVariables);

$arResult = array(
	"FOLDER" => $arParams["SEF_FOLDER"],
	"URL_TEMPLATES" => $arUrlTemplates,
	"VARIABLES" => $arVariables,
	"ALIASES" => $arVariableAliases,
);

$this->IncludeComponentTemplate($componentPage);