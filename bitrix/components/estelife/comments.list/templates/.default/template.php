<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<div class="comments">
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
		<div class="success">Ваш комментарий успешно добавлен. Комментарий появится после модерации.</div>
	<?php endif?>
	<form name="comments" method="post" action="#comment">
		<div class="form-in quality-in">
			<input type="hidden" name="id" value="<?=$arParams['element_id']?>">
			<input type="hidden" name="type" value="<?=$arParams['type']?>">
			<div class="col1">
				<div class="field <?=(isset($arResult['error']['first_name']) ? ' error' : '')?>">
					<label for="first_name">Имя</label>
					<input type="text" name="first_name" id="first_name" class="text" value="<?=$_POST['first_name']?>">
				</div>
				<div class="field <?=(isset($arResult['error']['last_name']) ? ' error' : '')?>">
					<label for="last_name">Фамилия</label>
					<input type="text" name="last_name" id="last_name" class="text" value="<?=$_POST['last_name']?>">
				</div>
			</div>
			<div class="col2">
				<div class="field <?=(isset($arResult['error']['comment']) ? ' error' : '')?>">
					<label for="comment">Ваш комментарий<span>Осталось <s>1000 символов</s></span></label>
					<textarea name="comment" id="comment"><?=$_POST['comment']?></textarea>
				</div>
			</div>
		</div>
		<input type="submit" class="submit" value="Комментировать" name="send_comment">
		<p class="total_error <?=(isset($arResult['error']) ? ' error' : '')?>">! Все поля обязательны к заполнению</p>
	</form>
</div>