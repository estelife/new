<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();
?>
<div class="content">
	<?php
	$APPLICATION->IncludeComponent(
		"estelife:sponsors.detail",
		"",
		array(
			"PREFIX"=>$arResult['PREFIX'],
			'ID'=>$arResult['ID']
		),
		false
	);
	?>
	<?php
	$APPLICATION->IncludeComponent(
		"estelife:sponsors.list.filter",
		"",
		array(),
		false
	);
	?>
	<div class="adv adv-out right">
		<?$APPLICATION->IncludeComponent("bitrix:advertising.banner","",Array(
				"TYPE" => "main_right_1",
				"CACHE_TYPE" => "A",
				"NOINDEX" => "N",
				"CACHE_TIME" => "3600"
			)
		);?>
	</div>
<!--	<div class="adv adv-out right">-->
<!--		--><?//$APPLICATION->IncludeComponent("bitrix:advertising.banner","",Array(
//				"TYPE" => "main_right_2",
//				"CACHE_TYPE" => "A",
//				"NOINDEX" => "N",
//				"CACHE_TIME" => "3600"
//			)
//		);?>
<!--	</div>-->
<!--	<div class="adv top">-->
<!--		--><?//$APPLICATION->IncludeComponent("bitrix:advertising.banner","",Array(
//				"TYPE" => "main_center_1",
//				"CACHE_TYPE" => "A",
//				"NOINDEX" => "N",
//				"CACHE_TIME" => "3600"
//			)
//		);?>
<!--	</div>-->
</div>