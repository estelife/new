<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
?>
<form action="" method="post" class="quality">
	<p>! Все поля обязательны к заполнению</p>
	<div class="field">
		<label for="name">Представьтесь, пожалуйста</label>
		<input type="text" class="text" name="name" id="name" />
	</div>
	<div class="field">
		<label for="email">Контактный e-mail</label>
		<input type="text" class="text" name="email" id="email" />
	</div>
	<div class="field phone">
		<label for="phone_code">Контактный телефон</label>
		<span>+7</span>
		<input type="text" class="text" name="phone[code]" id="phone_code" />
		<input type="text" class="text" name="phone[number]" id="phone_number" />
	</div>
	<div class="field clinic">
		<label for="clinic">Какую клинику Вы представляете?</label>
		<input type="text" class="text" id="clinic" name="clinic" />
	</div>
	<div class="field city">
		<label for="city">Город расположения центрального офиса</label>
		<input type="text" class="text" name="city" id="city" />
	</div>
	<input type="submit" class="submit" value="Отправить заявку" />
</form>