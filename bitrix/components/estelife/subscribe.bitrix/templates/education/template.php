<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<form action="" method="post" class="subscribe main" data-success="Спасибо! Вы успешно подписаны на получение новостей о предстоящем вебинаре." data-success-tag="p">
	<?php if($arResult['success']):?>
		<p class="req-success">Спасибо! Вы успешно подписаны на получение новостей о предстоящем вебинаре.</p>
	<?php else:?>
		<p>Оставьте нам свой e-mail и мы сможем держать вас в курсе новостей по данному вебинару</p>
		<div class="field form-in <?=(isset($arResult['errors']['email']) ? ' error' : '')?>">
			<input type="text" name="email" class="text" placeholder="Ваш e-mail..." value="<?=$_POST['email']?>">
			<input type="hidden" name="type" value="10">
			<input name="go" value="OK" type="submit" class="submit" />
		</div>
	<?php endif?>
</form>