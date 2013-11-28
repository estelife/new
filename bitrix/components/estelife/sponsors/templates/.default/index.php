<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?php
$APPLICATION->IncludeComponent(
	"estelife:sponsors.list",
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
//			"estelife:organizatory.list.filter",
//			"",
//			array(),
//			false
//		);
//		?>
<!--	</div>-->

	<? include_sidebar(); ?>
