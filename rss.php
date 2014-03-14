<?
define("ADMIN_SECTION", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
header("Content-Type: text/xml");
CModule::IncludeModule("iblock");


getAllRss('news');

function getAllRss($type){
	echo "<"."?xml version=\"1.0\" encoding=\"".LANG_CHARSET."\"?".">\n";
	echo "<rss version=\"2.0\"";
	echo ">\n";

	$dbrPodcast = CIBlock::GetList(array(), array(
		"type" => $type,
		"ACTIVE" => "Y",
		"ID" => 36,
	));
	$dbrNews = CIBlock::GetList(array(), array(
		"type" => $type,
		"ACTIVE" => "Y",
		"ID" => 3,
	));
	$dbrExp = CIBlock::GetList(array(), array(
		"type" => $type,
		"ACTIVE" => "Y",
		"ID" => 35,
	));
	$arIBlockPodcast = $dbrPodcast->Fetch();
	$arIBlockNews = $dbrNews->Fetch();
	$arIBlockExp = $dbrExp->Fetch();

	echo CIBlockRSS::GetRSSText($arIBlockPodcast);
	echo CIBlockRSS::GetRSSText($arIBlockNews);
	echo CIBlockRSS::GetRSSText($arIBlockExp);

	echo "</rss>\n";
}