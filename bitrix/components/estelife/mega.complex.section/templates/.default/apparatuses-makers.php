<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<div class="content">
	<?php
	$APPLICATION->IncludeComponent(
		"estelife:apparatuses-makers.list",
		"",
		array(
			"PAGE_COUNT" => 10,
		),
		false
	);
	?>
	<?php
	$APPLICATION->IncludeComponent(
		"estelife:apparatuses-makers.list.filter",
		"",
		array(),
		false
	);
	?>
	<?$APPLICATION->IncludeComponent("bitrix:advertising.banner","right",Array(
			"TYPE" => "main_right_1",
			"CACHE_TYPE" => "A",
			"NOINDEX" => "N",
			"CACHE_TIME" => "3600"
		)
	);?>
	<?$APPLICATION->IncludeComponent("bitrix:advertising.banner","right",Array(
			"TYPE" => "main_right_2",
			"CACHE_TYPE" => "A",
			"NOINDEX" => "N",
			"CACHE_TIME" => "3600"
		)
	);?>
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