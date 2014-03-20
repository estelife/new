<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<div class="comments" data-id="<?=$arParams['element_id']?>" data-type="<?=$arParams['type']?>">
	<h2>Обсуждение</h2>
	<?php if (!empty($arResult['comments'])):?>
		<b class="stat"><?=$arResult['count']?></b>
		<div class="items">
			<?php foreach ($arResult['comments'] as $val):?>
				<div class="item" id="comment_<?=$val['id']?>">
					<b><?=$val['name']?></b>
					<i><?=$val['date_create']?></i>
					<p><?=$val['text']?></p>
				</div>
			<?php endforeach?>
			<div class="more">
				<?php if ($arResult['all']):?>
					<a href="#"><span class="hide">Скрыть комментарии</span></a>
				<?php else:?>
					<a href="#"><span>Все комментарии</span></a>
				<?php endif?>
			</div>
		</div>
	<?php endif?>
	<?php if(!empty($arResult['success'])):?>
		<div class="success">Ваш комментарий успешно добавлен.</div>
	<?php endif?>
	<?php if($arResult['auth']):?>
		<?$APPLICATION->IncludeComponent(
			"estelife:forms",
			"add_comment",
			array(
				'FORM'=>$arResult['form'],
				'ERRORS'=>$arResult['error'],
			)
		)?>
	<?php else:?>
		<div class="not-auth">
			<p>Комментарии могут оставлять только зарегистрированные пользователи. <a href="/personal/register/?backurl=/<?=$arResult['type_string']?><?=$arParams['element_id']?>/">Зарегистрироваться</a> или <a href="/personal/auth/?backurl=/<?=$arResult['type_string']?><?=$arParams['element_id']?>/">авторизоваться</a>.</p>
		</div>
	<?php endif?>
</div>