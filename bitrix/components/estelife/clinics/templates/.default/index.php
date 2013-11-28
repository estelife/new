<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?php
$APPLICATION->IncludeComponent(
	"estelife:clinics.list",
	"",
	array(
		"PAGE_COUNT" => 10
	),
	false
);
?>
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
