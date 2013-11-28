<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?php
$APPLICATION->IncludeComponent(
	"estelife:training_centers.detail",
	"",
	array(
		'ORG_NAME'=>$arResult['VARIABLES']['ORG_NAME'],
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
			"estelife:training_centers.list.filter",
			"",
			array(),
			false
		);
		?>
	</div>
	-->

	<? include_sidebar(); ?>
