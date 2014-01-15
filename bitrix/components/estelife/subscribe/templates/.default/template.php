<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<form name="subscribe" method="post" action="" class="subscribe">
	<div class="field">
		<input type="text" name="email" class="text" placeholder="Ваш e-mail..." />
	</div>
	<div class="field check">
		<input type="checkbox" name="always" value="1" id="always" />
		<label for="always"><?=$arParams['text']?></label>
		<input type="hidden" name="type" value="<?=$arParams['type']?>" />
		<?php if (!empty($arParams['params'])):?>
			<?php foreach ($arParams['params'] as $key=>$val):?>
				<input type="hidden" name="params[<?=$key?>]" value="<?=$val?>" />
			<?php endforeach?>
		<?php endif?>
	</div>
	<input type="submit" class="submit" value="Оставить" />
</form>
