<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?php
$APPLICATION->IncludeComponent(
	"estelife:trainings.detail",
	"",
	array(
		'TRAIN_NAME'=>$arResult['VARIABLES']['TRAIN_NAME'],
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
			"estelife:trainings.list.filter",
			"",
			array(),
			false
		);
		?>
	</div>
	-->

	<? include_sidebar(); ?>
