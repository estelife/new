<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?php if(!empty($arResult)):?>
	<div class="notices">
		<?php foreach ($arResult['success'] as $val):?>
			<div class="item">
				<div class="title">
					<?=$val['title']?>
				</div>
				<div class="description">
					<?=$val['description']?>
				</div>
			</div>
		<?php endforeach?>
		<?php foreach ($arResult['errors'] as $val):?>
			<div class="item">
				<div class="title">
					<?=$val['title']?>
				</div>
				<div class="description">
					<?=$val['description']?>
				</div>
			</div>
		<?php endforeach?>
	</div>
<?php endif?>
