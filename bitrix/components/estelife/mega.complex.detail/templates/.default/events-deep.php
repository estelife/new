<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

$arPath = explode('/', GetPagePath());
$arPath = array_splice($arPath, 1, -1);

preg_match('/^([a-z]{2})([0-9]+)$/',$arPath[0], $mathcesEvent);
$sEventHall =  preg_match_all('/(.*)-(.*)/', $arPath[1], $mathcesHall);

if($sEventHall == 1){
	$sHallDate = $mathcesHall[2][0];
	$sYear = date('Y');
	$sHallDate .='.'.$sYear;
}
?>

<div class="content">
	<?php
	if($sEventHall == 1){
		$APPLICATION->IncludeComponent(
			"estelife:events.hall",
			"",
			array(
				"HALL"=>$mathcesHall[1][0],
				'DATE'=>$sHallDate,
				'EVENT_ID'=>$arResult['ID'],
			),
			false
		);
	}else{
		$APPLICATION->IncludeComponent(
			"estelife:events.program",
			"",
			array(
				'EVENT_ID'=>$arResult['ID']
			),
			false
		);
	}
	?>
	<!--<div class="adv adv-out right">
		<?/*$APPLICATION->IncludeComponent("bitrix:advertising.banner","",Array(
				"TYPE" => "main_right_1",
				"CACHE_TYPE" => "A",
				"NOINDEX" => "N",
				"CACHE_TIME" => "3600"
			)
		);*/?>
	</div>-->
</div>