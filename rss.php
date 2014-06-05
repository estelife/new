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

	$dbrPodcast =CIBlockElement::GetList(
		array('TIMESTAMP_X'=>'DESC','DATE_CREATE'=>'DESC'),
		array(
			'IBLOCK_ID' => 36,
			'ACTIVE' => 'Y',
			'ACTIVE_DATE'=>'Y',
		),
		false,
		false,
		array(
			'ID',
			'NAME',
			'SECTION_ID',
			'DATE_CREATE',
			'PREVIEW_TEXT',
			'PROPERTY_TEXT_IN_HOME',
			'PROPERTY_COUNT',
			'PROPERTY_FRONTRIGHT',
			'PROPERTY_FRONTBIG',
			'DATE_CHANGE',
		)
	);
	$dbrNews =CIBlockElement::GetList(
		array('TIMESTAMP_X'=>'DESC','DATE_CREATE'=>'DESC'),
		array(
			'IBLOCK_ID' => 3,
			'ACTIVE' => 'Y',
			'ACTIVE_DATE'=>'Y',
		),
		false,
		false,
		array(
			'ID',
			'NAME',
			'SECTION_ID',
			'DATE_CREATE',
			'PREVIEW_TEXT',
			'PROPERTY_TEXT_IN_HOME',
			'PROPERTY_COUNT',
			'PROPERTY_LISTIMG',
		)
	);
	$dbrExp = CIBlockElement::GetList(
		array('TIMESTAMP_X'=>'DESC','DATE_CREATE'=>'DESC'),
		array(
			'IBLOCK_ID' => 35,
			'ACTIVE' => 'Y',
			'ACTIVE_DATE'=>'Y',
		),
		false,
		false,
		array(
			'ID',
			'NAME',
			'SECTION_ID',
			'DATE_CREATE',
			'PREVIEW_TEXT',
			'PROPERTY_TEXT_IN_HOME',
			'PROPERTY_COUNT',
			'PROPERTY_FRONTRIGHT',
			'PROPERTY_FRONTBIG'
		)
	);
	$arIBlockPodcast = $dbrPodcast->Fetch();
	$arIBlockNews = $dbrNews->Fetch();
	$arIBlockExp = $dbrExp->Fetch();


	$arPodcasts = array();
	$arNews = array();
	$arExp =array();
	$i = 0;

	while($arPodc = $dbrPodcast->Fetch()){
			$asRes['IMG_B'] = CFile::GetFileArray($arPodc['PROPERTY_FRONTBIG_VALUE']);
			$asRes['IMG_B']=$asRes['IMG_B']['SRC'];
			$arPodcasts[$i]['ID'] = $arPodc['ID'];
			$arPodcasts[$i]['NAME'] = $arPodc['NAME'];
			$arPodcasts[$i]['PREVIEW_TEXT'] = $arPodc['PREVIEW_TEXT'];
			$arPodcasts[$i]['IMAGE_SRC'] = $asRes['IMG_B'];
			$arPodcasts[$i]['LINK'] = 'pt'.$arPodc['ID'];
			$arPodcasts[$i]['DATE_CREATE'] = $arPodc['DATE_CREATE'];
			$i++;
		}

	while($arNew = $dbrNews->Fetch()){
			$asRes['IMG_B'] = CFile::GetFileArray($arNew['PROPERTY_LISTIMG_VALUE']);
			$asRes['IMG_B']=$asRes['IMG_B']['SRC'];
			$arNews[$i]['ID'] = $arNew['ID'];
			$arNews[$i]['NAME'] = $arNew['NAME'];
			$arNews[$i]['PREVIEW_TEXT'] = $arNew['PREVIEW_TEXT'];
			$arNews[$i]['IMAGE_SRC'] = $asRes['IMG_B'];
			$arNews[$i]['LINK'] = 'ns'.$arNew['ID'];
			$arNews[$i]['DATE_CREATE'] = $arNew['DATE_CREATE'];
			$i++;
	}

	while($arEx = $dbrExp->Fetch()){

		$asRes['IMG_B'] = CFile::GetFileArray($arEx['PROPERTY_FRONTBIG_VALUE']);
		$asRes['IMG_B']=$asRes['IMG_B']['SRC'];
		$arExp[$i]['ID'] = $arEx['ID'];
		$arExp[$i]['NAME'] = $arEx['NAME'];
		$arExp[$i]['PREVIEW_TEXT'] = $arEx['PREVIEW_TEXT'];
		$arExp[$i]['IMAGE_SRC'] = $asRes['IMG_B'];
		$arExp[$i]['LINK'] = 'pt'.$arEx['ID'];
		$arExp[$i]['DATE_CREATE'] = $arEx['DATE_CREATE'];
		$i++;
	}

	echo '<channel>';
	echo "<title>Подкасты</title>";
	echo "<link>http://estelife.ru</link>";
	echo "<description>Подкасты</description>";
	echo "<ttl>24</ttl>";

	foreach($arPodcasts as $value){
		$date = strtotime($value['DATE_CREATE']);
		$preview_text = str_replace('&','',$value['PREVIEW_TEXT']);

		echo "<item>";
		echo '<title>'.$value['NAME'].'</title>';
		echo '<link>http://estelife.ru/pt'.$value['ID'].'</link>';
		echo '<description>'.$preview_text.'</description>';
		echo '<pubDate>'.gmdate("D, d M Y H:i:s", $date)." GMT".'</pubDate>';
		if(!empty($value['IMAGE_SRC'])){
		echo '<image>';
		echo '<url>http://estelife.ru'.$value['IMAGE_SRC'].'</url>';
		echo '<title>'.$value['name'].'</title>';
		echo '<link>http://estelife.ru/</link>';
		echo '<width>88</width><height>31</height>';
		echo '</image>';
		}
		echo '</item>';
	}
	echo '</channel>';

	echo '<channel>';
	echo "<title>Новости</title>";
	echo "<link>http://estelife.ru</link>";
	echo "<description>Новости</description>";
	echo "<ttl>24</ttl>";

	foreach($arNews as $value){
		$date = strtotime($value['DATE_CREATE']);
		$preview_text = str_replace('&','',$value['PREVIEW_TEXT']);

		echo "<item>";
		echo '<title>'.$value['NAME'].'</title>';
		echo '<link>http://estelife.ru/pt'.$value['ID'].'</link>';
		echo '<description>'.$preview_text.'</description>';
		echo '<pubDate>'.gmdate("D, d M Y H:i:s", $date)." GMT".'</pubDate>';
		if(!empty($value['IMAGE_SRC'])){
		echo '<image>';
		echo '<url>http://estelife.ru'.$value['IMAGE_SRC'].'</url>';
		echo '<title>'.$value['name'].'</title>';
		echo '<link>http://estelife.ru/</link>';
		echo '<width>88</width><height>31</height>';
		echo '</image>';
		}
		echo '</item>';
	}
	echo '</channel>';

	echo '<channel>';
	echo "<title>Экспертное мнение</title>";
	echo "<link>http://estelife.ru</link>";
	echo "<description>Экспертное мнение</description>";
	echo "<ttl>24</ttl>";

	foreach($arExp as $value){
		$date = strtotime($value['DATE_CREATE']);
		$preview_text = str_replace('&','',$value['PREVIEW_TEXT']);

		echo "<item>";
		echo '<title>'.$value['NAME'].'</title>';
		echo '<link>http://estelife.ru/pt'.$value['ID'].'</link>';
		echo '<description>'.$preview_text.'</description>';
		echo '<pubDate>'.gmdate("D, d M Y H:i:s", $date)." GMT".'</pubDate>';
		if(!empty($value['IMAGE_SRC'])){
		echo '<image>';
		echo '<url>http://estelife.ru'.$value['IMAGE_SRC'].'</url>';
		echo '<title>'.$value['name'].'</title>';
		echo '<link>http://estelife.ru/</link>';
		echo '<width>88</width><height>31</height>';
		echo '</image>';
		}
		echo '</item>';
	}
	echo '</channel>';



	/*echo CIBlockRSS::GetRSSText($arIBlockPodcast);
	echo CIBlockRSS::GetRSSText($arIBlockNews);
	echo CIBlockRSS::GetRSSText($arIBlockExp);*/

	echo "</rss>\n";
}