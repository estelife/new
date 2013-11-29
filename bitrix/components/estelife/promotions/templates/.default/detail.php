<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?php
$APPLICATION->IncludeComponent(
	"estelife:promotions.detail",
	"",
	array(
		'ACTION_NAME'=>$arResult['VARIABLES']['ACTION_NAME']
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
			"estelife:promotions.list.filter",
			"",
			array(),
			false
		);
		?>
	</div>
	-->

	<? include_sidebar(); ?>
