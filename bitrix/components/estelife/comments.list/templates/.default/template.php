<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<div class="comments" data-id="<?=$arParams['element_id']?>" data-type="<?=$arParams['type']?>">
	<h2>Обсуждение</h2>
	<?php if (!empty($arResult['comments'])):?>
		<b class="stat"><?=$arResult['count']?></b>
		<div class="items">
			<?php foreach ($arResult['comments'] as $val):?>
				<div class="item">
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
		<form name="comments" method="post" action="#comment">
			<div class="form-in quality-in">
				<input type="hidden" name="id" value="<?=$arParams['element_id']?>">
				<input type="hidden" name="type" value="<?=$arParams['type']?>">
				<div class="col1">
					<div class="field <?=(isset($arResult['error']['comment']) ? ' error' : '')?>">
						<label for="comment">Ваш комментарий<span>Осталось <s>1000 символов</s></span></label>
						<textarea name="comment" id="comment"><?=$_POST['comment']?></textarea>
					</div>
				</div>
			</div>
			<input type="submit" class="submit" value="Комментировать" name="send_comment">
			<p class="total_error <?=(isset($arResult['error']) ? ' error' : '')?>">! Все поля обязательны к заполнению</p>
		</form>
	<?php else:?>
		<div class="not-auth">
			<p>Комментарии могут оставлять только зарегистрированные пользователи. <a href="/personal/register/?backurl=/<?=$arParams['type']?><?=$arParams['element_id']?>/">Зарегистрироваться</a>.</p>
		</div>
	<?php endif?>
</div>