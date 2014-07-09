<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="inner">
	<ul class="crumb">
		<li><a href="/">Главная</a></li>
		<li><a href="/threads-makers/">Производители нитей</a></li>
		<li><b><?=$arResult['company']['name']?></b></li>
	</ul>
	<div class="wrap_item">
		<div class="item detail producer">
			<h1><?=$arResult['company']['name']?></h1>
			<div class="current">
				<div class="img">
					<div class="img-in">
						<?=$arResult['company']['img']?>
					</div>
				</div>
				<ul>
					<?php if (!empty($arResult['company']['country_name'])):?>
						<li class="country c<?=$arResult['company']['country_id']?>"><?=$arResult['company']['country_name']?></li>
					<?php endif?>
					<?php if (!empty($arResult['company']['web'])):?>
						<li><a href="<?=$arResult['company']['web']?>" target="_blank"><?=$arResult['company']['web_short']?></a></li>
					<?php endif?>
				</ul>
			</div>
			<p><?=$arResult['company']['detail_text']?></p>
			<?$APPLICATION->IncludeComponent(
				"estelife:threads.list",
				"maker_list",
				array(
					"MAKER"=>$arResult['company']['id'],
					"COMPONENT"=> 'maker_list',
				)
			)?>
		</div>
	</div>
</div>