<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?php if (!empty($arResult['form'])):?>
	<form name="<?=$arResult['form']['name']?>" method="<?=$arResult['form']['method']?>" action="<?=$arResult['form']['action']?>" id="<?=$arResult['form']['id']?>">
		<div class="form-in quality-in">
			<?php echo $arResult['form']['fields']['id']?>
			<?php echo $arResult['form']['fields']['type']?>
			<div class="col1">
				<div class="field <?=(isset($arResult['errors']['comment']) ? ' error' : '')?>">
					<label for="comment">Ваш комментарий<span>Осталось <s>1000 символов</s></span></label>
					<?php echo $arResult['form']['fields']['comment']?>
				</div>
			</div>
		</div>
		<?php echo $arResult['form']['fields']['send_comment']?>
	</form>
<?php endif?>
