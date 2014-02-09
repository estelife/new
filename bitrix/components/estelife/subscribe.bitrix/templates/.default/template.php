<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<form action="" method="post" class="subscribe main">
	<?php if($arResult['success']):?>
		<h3>Вы успешно подписались на новые статьи!</h3>
	<?php else:?>
		<h3>Хотите всегда быть в курсе?</h3>
		<div class="field <?=(isset($arResult['errors']['email']) ? ' error' : '')?>">
			<input type="text" name="email" class="text" placeholder="Ваш e-mail..." value="<?=$_POST['email']?>">
			<input type="hidden" name="type" value="3">
		</div>
		<input name="go" value="OK" type="submit" class="submit" />
	<?php endif?>
</form>