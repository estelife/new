<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="auth">
	<a href="/" class="logo no-ajax">
		Портал эстетической медицины
	</a>
	<form action="" method="post" name="forgotpswd" class="quality-in">
		<?php if (!empty($arResult['success'])):?>
			<div class="success"><?=$arResult['success']?></div>
		<?php endif?>
		<?php if (!empty($arResult['global_errors'])):?>
			<div class="global_error">
				<?=$arResult['global_errors']['confirm']?>
				<?=$arResult['global_errors']['pswd']?>
			</div>
		<?php endif?>
		<?php if(!empty($arResult["back_url"])):?>
			<input type="hidden" name="backurl" value="<?=$arResult["backurl"]?>" />
		<?php endif?>

		<?php if ($arResult['change_password']):?>
			<input type="hidden" name="change_password" value="1" />
			<input type="hidden" name="user_id" value="<?=$arResult['user_id']?>" />
			<div class="field <?=(isset($arResult['errors']['pswd']) ? ' error' : '')?>">
				<label for="pswd">Пароль</label>
				<input type="password" class="text" name="pswd" id="pswd" placeholder="..." />
			</div>
			<div class="field <?=(isset($arResult['errors']['pswd']) ? ' error' : '')?>">
				<label for="c_pswd">Подтверждение пароля</label>
				<input type="password" class="text" name="c_pswd" id="c_pswd" placeholder="..." />
			</div>
			<input type="submit" class="submit" value="Изменить" />
			<a href="/personal/auth/?backurl=<?=$arResult["backurl"]?>" class="link register">Войти</a>
			<a href="<?=$arResult["backurl"]?>" class="link back"><span>Вернуться на страницу<i></i></span></a>
		<?php else:?>
			<input type="hidden" name="send_check_forgotpswd" value="1" />
			<div class="field <?=(isset($arResult['errors']['email']) ? ' error' : '')?>">
				<label for="email">E-mail</label>
				<input type="text" class="text" name="email" id="email" placeholder="..." value="<?=$_POST['email']?>" />
				<?php if(isset($arResult['errors']['email'])): ?>
					<i><?=$arResult['errors']['email']?></i>
				<?php endif; ?>
			</div>
			<input type="submit" class="submit" value="Восстановить" />
			<a href="/personal/auth/?backurl=<?=$arResult["backurl"]?>" class="link register">Войти</a>
			<a href="<?=$arResult["backurl"]?>" class="link back"><span>Вернуться на страницу<i></i></span></a>
		<?php endif?>
	</form>
	<div class="empty"></div>
</div>