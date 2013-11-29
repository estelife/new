<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?php
$APPLICATION->IncludeComponent(
	"estelife:events.detail",
	"",
	array(
		'EVENT_NAME'=>$arResult['VARIABLES']['EVENT_NAME'],
	),
	false
);
?>
</div>
<div class="sidebar">
	<!--
	<div class="small-block">
		<?php
		$APPLICATION->IncludeComponent(
			"estelife:events.list.filter",
			"",
			array(),
			false
		);
		?>
	</div>
	-->

	<? include_sidebar(); ?>
