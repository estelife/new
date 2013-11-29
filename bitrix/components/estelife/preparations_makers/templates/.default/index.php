<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?php
$APPLICATION->IncludeComponent(
	"estelife:preparations_makers.list",
	"",
	array(
		"PAGE_COUNT" => 10
	),
	false
);
?>
</div>
<div class="sidebar">
<!--	<div class="small-block">-->
<!--		--><?php
//		$APPLICATION->IncludeComponent(
//			"estelife:maker_pills.list.filter",
//			"",
//			array(),
//			false
//		);
//		?>
<!--	</div>-->

	<? include_sidebar(); ?>
