<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?php
$APPLICATION->IncludeComponent(
	"estelife:preparations_makers.detail",
	"",
	array(
		'PILL_NAME'=>$arResult['VARIABLES']['PILL_NAME'],
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
			"estelife:preparations_makers.list.filter",
			"",
			array(),
			false
		);
		?>
	</div>
	-->

	<? include_sidebar(); ?>
