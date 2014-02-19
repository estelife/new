<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
?>
<form action="<?=($arResult['is_login'] ? $arResult['form_action'] : '')?>" method="post" class="p-form">
	<h2>Регистрация и оплата <a href="/personal/auth/?backurl=%2Feducation%2F">Вход</a></h2>
	<input type="hidden" name="project" value="<?=$arResult['project_id']?>" />
	<input type="hidden" name="source" value="<?=$arResult['source_id']?>" />

	<div class="field">
		<label for="empty">Стоимость</label>
		<input type="text" class="text" name="empty" id="empty" value="<?=$arResult['amount']?> руб." disabled="disabled" />
	</div>

	<?php if(!$arResult['is_login']):?>
		<div class="field <?=(isset($arResult['errors']['name']) ? ' error' : '')?>">
			<label for="name">ФИО</label>
			<input type="text" class="text" name="name" id="name" placeholder="..." value="<?=$_POST['name']?>"/>
			<?php if(isset($arResult['errors']['name'])): ?>
				<i><?=$arResult['errors']['name']?></i>
			<?php endif; ?>
		</div>
		<div class="field <?=(isset($arResult['errors']['login']) ? ' error' : '')?>">
			<label for="email">E-mail</label>
			<input type="text" class="text" name="login" id="login" placeholder="..." value="<?=$_POST['login']?>" />
			<?php if(isset($arResult['errors']['login'])): ?>
				<i><?=$arResult['errors']['login']?></i>
			<?php endif; ?>
		</div>
		<div class="field <?=(isset($arResult['errors']['password']) ? ' error' : '')?>">
			<label for="password">Придумайте пароль</label>
			<input type="password" class="text" name="password" id="password" placeholder="..." />
			<?php if(isset($arResult['errors']['password'])): ?>
				<i><?=$arResult['errors']['password']?></i>
			<?php endif; ?>
		</div>
	<?php else: ?>
		<input type="hidden" name="nickname" value="<?=$arResult['receipt_id']?>" />
		<input type="hidden" name="amount" value="<?=$arResult['amount']?>" />
	<?php endif; ?>

	<div class="field mode_type">
		<label for="mode_type">Способ оплаты</label>
		<select name="mode_type" id="mode_type">
			<option value="468">Банковская карта</option>
		</select>
	</div>

	<input type="submit" class="submit" value="Оплатить" />
</form>