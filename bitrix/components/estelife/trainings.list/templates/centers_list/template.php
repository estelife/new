<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?php if (!empty($arResult['training'])):?>
	<?php foreach ($arResult['training'] as $val):?>
		<div class="item training">
			<div class="item-rel">
				<h2><a href="<?=$val['link']?>"><?=$val['name']?></a></h2>
				<p><?=$val['preview_text']?></p>
				Период проведения: <b><?=$val['first_period']['from']?>
					<?php if(!empty($val['first_period']['to'])):?>
						-
						<?=$val['first_period']['to']?>
					<?php endif; ?></b><br>
				<span class="date"><?=$val["first_date"]?></span>
			</div>
			<div class="border"></div>
		</div>
	<?php endforeach?>
<?php else:?>
	<div class="default">
		<h3>Текущих семинаров нет</h3>
		<p>На текущий момент учебный центр <?=$arParams['MAKER_NAME']?> не проводит семинаров.</p>
		<p>Однако, Вы можете оставить нам свой e-mail, и мы с радостью сообщим Вам о запуске новых семинаров от данного учебного центра.</p>
		<?$APPLICATION->IncludeComponent(
			"estelife:subscribe",
			"",
			array(
				'params'=>array('id'=>$arParams['MAKER'],'city_id'=>$arResult['city']),
				'type'=>2,
				'text'=>'Хочу узнавать обо всех новых семинарах, размещаемых на портале'
			)
		)?>
	</div>
<?php endif?>