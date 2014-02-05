<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="auth">
	<a href="/" class="logo no-ajax">
		Портал эстетической медицины
	</a>
	<form action="" method="post" name="auth" class="quality-in">
		<?php if (!empty($arResult['errors'])):?>
			<div class="global_error">
				<?=$arResult['errors']['auth']?>
				<?=$arResult['errors']['confirm']?>
			</div>
		<?php endif?>
		<?php if(!empty($arResult["back_url"])):?>
			<input type="hidden" name="backurl" value="<?=$arResult["backurl"]?>" />
		<?php endif?>
		<div class="field <?=(isset($arResult['errors']['auth']) ? ' error' : '')?>">
			<label for="email">E-mail/Логин</label>
			<input type="text" class="text" name="login" id="login" placeholder="..."  value="<?=$_POST['login']?>" />
			<input type="checkbox" class="text" name="remember" value="1" title="Запомнить пароль" />
		</div>
		<div class="field <?=(isset($arResult['errors']['auth']) ? ' error' : '')?>">
			<label for="email">Пароль</label>
			<input type="password" class="text" name="password" id="password" placeholder="..." />
			<a href="/personal/forgotpswd/?backurl=<?=$arResult["backurl"]?>" class="remember">Напомнить пароль</a>
		</div>
		<input type="submit" class="submit" value="Войти" />
		<a href="/personal/register/?backurl=<?=$arResult["backurl"]?>" class="link register">Регистрация</a>
		<a href="<?=$arResult["backurl"]?>" class="link back"><span>Вернуться на страницу<i></i></span></a>
	</form>
	<div class="empty"></div>
</div>