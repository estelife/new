<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<div class="block" rel="news">
	<div class="block-header red">
		<span>Клиники</span>
		<div class="clear"></div>
	</div>
	<div class="shadow"></div>
	<div class="tab-group" rel="articles">

		<?php
		$APPLICATION->IncludeComponent(
			"estelife:clinics.list",
			"",
			array(
				"PAGE_COUNT" => 10,
				'CITY_CODE'=>$arResult['VARIABLES']['CITY_CODE']
			),
			false
		);
		?>

	</div>
</div>
</div>
<div class="sidebar">
	<div class="small-block el-filter">
		<?php
		$APPLICATION->IncludeComponent(
			"estelife:clinics.list.filter",
			"",
			array(),
			false
		);
		?>
	</div>

	<? include_sidebar(); ?>
