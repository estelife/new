<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<script type="text/javascript" src="/js/subscr/jquery-ui-1.8.16.custom.min.js"></script>
<?if (!empty($arResult['RUBRICS'])):?>
	<?if(count($arResult["ERRORS"]) > 0):?>

		<?foreach($arResult["ERRORS"] as $strError):?>
			<p class="errortext"><?echo $strError?></p>
		<?endforeach?>
	<?else:?>
	<div class="sb_b block_e subscr_sb">
		<div class="form_c_a">
			<div class="hl">Подписаться</div>
			<form action="" method="POST">
				<fieldset>
					<div class="f_line">
							<?$i=1?>
							<?foreach ($arResult['RUBRICS'] as $val):?>
								<?$checked = 0?>
								<?if (in_array($val['ID'], $arResult['sub'])) $checked = 1;?>
								
								<input type="checkbox" name="RUB_ID[]" value="<?=$val['ID']?>" <?if ($checked == 1):?>checked="checked"<?endif?> id="opt_c3_<?=$i?>" class="sub_check">
									<label for="opt_c3_<?=$i?>" aria-pressed="true" class="ui-button ui-widget ui-state-default ui-corner-right ui-button-text-icon-primary" role="button" aria-disabled="false">
										<span class="ui-button-text"><?=$val['NAME']?></span></label>

							<?$i++?>
							<?endforeach?>
					</div>
					<?if (!$USER->IsAuthorized()):?>
					<div class="f_line">
						<input type="text" name="EMAIL" class="txt_c txt_def" value="Ваш email">
					</div>
					<?endif?>
					<div class="f_line fl_sub">
						<span><a href="#">Подписаться</a></span>
					</div>
				</fieldset>
			</form>
		</div>
	</div>
	<?endif?>
<?endif?>