<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<div class="auth">
	<a href="/" class="logo no-ajax">
		Портал эстетической медицины
	</a>
	<form action="" method="post" name="auth" class="quality-in">
		<?php if($arResult['values']["USER_ID"]>0):?>
			<div class="success">На Ваш E-mail отправлено письмо с подтверждением регистрации.</div>
		<?php endif?>
		<?php if(!empty($arResult["back_url"])):?>
			<input type="hidden" name="backurl" value="<?=$arResult["backurl"]?>" />
		<?php endif?>
		<input type="hidden" name="register" value="1" />
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
			<label for="password">Пароль</label>
			<input type="password" class="text" name="password" id="password" placeholder="..." />
			<?php if(isset($arResult['errors']['password'])): ?>
				<i><?=$arResult['errors']['password']?></i>
			<?php endif; ?>
		</div>
		<input type="submit" class="submit" value="Зарегистрироваться" />
		<a href="/personal/auth/?backurl=<?=$arResult["backurl"]?>" class="link register">Войти</a>
		<a href="<?=$arResult["backurl"]?>" class="link back"><span>Вернуться на страницу<i></i></span></a>
	</form>
	<div class="empty"></div>
</div>