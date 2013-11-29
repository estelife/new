<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>

<?php
$APPLICATION->IncludeComponent(
	"estelife:promotions.list",
	"",
	array(
		"PAGE_COUNT" => 20
	),
	false
);
?>
</div>
<div class="sidebar">
	<div class="small-block el-filter">
		<?php
		$APPLICATION->IncludeComponent(
			"estelife:promotions.list.filter",
			"",
			array(),
			false
		);
		?>
	</div>

	<? include_sidebar(); ?>
