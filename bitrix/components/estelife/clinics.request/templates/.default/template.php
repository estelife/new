<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
?>
<?php if(!isset($arResult['step']) || $arResult['step']==1): ?>
	<form action="" method="post" name="add_request" class="quality">
		<div class="quality-in">
			<p>! Все поля обязательны к заполнению</p>
			<div class="field<?=(isset($arResult['error']['user_name']) ? ' error' : '')?>">
				<label for="user_name">Представьтесь, пожалуйста</label>
				<input type="text" class="text" name="user_name" id="user_name" value="<?=(isset($arResult['user_name']) ? $arResult['user_name'] : '')?>" />
				<input type="hidden" name="user_id" value="<?=(isset($arResult['user_id']) ? $arResult['user_id'] : 0)?>" />

				<?php if(isset($arResult['error']['user_name'])): ?>
					<i><?=$arResult['error']['user_name']?></i>
				<?php endif; ?>
			</div>
			<div class="field<?=(isset($arResult['error']['user_email']) ? ' error' : '')?>">
				<label for="user_email">Контактный e-mail</label>
				<input type="text" class="text" name="user_email" id="user_email" value="<?=(isset($arResult['user_email']) ? $arResult['user_email'] : '')?>" />

				<?php if(isset($arResult['error']['user_email'])): ?>
					<i><?=$arResult['error']['user_email']?></i>
				<?php endif; ?>
			</div>
			<div class="field phone<?=(isset($arResult['error']['phone_number']) ? ' error' : '')?>">
				<label for="phone_code">Контактный телефон</label>
				<span>+7</span>
				<input type="text" class="text" name="phone_code" id="phone_code" value="<?=(isset($arResult['phone_code']) ? $arResult['phone_code'] : '')?>" />
				<input type="text" class="text" name="phone_number" id="phone_number" value="<?=(isset($arResult['phone_number']) ? $arResult['phone_number'] : '')?>" />

				<?php if(isset($arResult['error']['phone_number'])): ?>
					<i><?=$arResult['error']['phone_number']?></i>
				<?php endif; ?>
			</div>
			<div class="field clinic<?=(isset($arResult['error']['company_name']) ? ' error' : '')?>">
				<label for="company_name">Какую клинику Вы представляете?</label>
				<input type="text" class="text preload" data-action="get_clinics" id="company_name" name="company_name" value="<?=(isset($arResult['company_name']) ? $arResult['company_name'] : '')?>" />
				<input type="hidden" name="company_id" value="0" value="<?=(isset($arResult['company_id']) ? $arResult['company_id'] : 0)?>" />

				<?php if(isset($arResult['error']['company_name'])): ?>
					<i><?=$arResult['error']['company_name']?></i>
				<?php endif; ?>
			</div>
			<div class="field city<?=(isset($arResult['error']['city_name']) ? ' error' : '')?>">
				<label for="city_name">Город расположения центрального офиса</label>
				<input type="text" class="text preload" data-action="get_cities_by_term" name="city_name" id="city_name" value="<?=(isset($arResult['city_name']) ? $arResult['city_name'] : '')?>" />
				<input type="hidden" name="city_id" value="<?=(isset($arResult['city_id']) ? $arResult['city_id'] : 0)?>" />

				<?php if(isset($arResult['error']['city_name'])): ?>
					<i><?=$arResult['error']['city_name']?></i>
				<?php endif; ?>
			</div>
			<input type="submit" class="submit" value="Отправить заявку" />
		</div>
	</form>
<?php elseif($arResult['step']==3): ?>
	<p class="quality-result">Спасибо. Заявка принята, в ближайшее время с Вами свяжется наш специалист.</p>
<?php endif; ?>