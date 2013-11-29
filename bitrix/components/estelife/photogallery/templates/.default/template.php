<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php if (!empty($arResult['photo'])):?>
	<?php foreach ($arResult['photo'] as $val):?>
		<div class="gallery <?php if($val['SMALL'] == 1):?> small <?php endif?>">
			<a href='<?=$val['URL']?>'>
				<img src='<?=$val['SRC']?>' class='photo' />
				<span class='overlay'><i></i><span><?php if($val['SMALL'] == 0):?><?=$val['NAME']?><?php endif?></span></span>
			</a>
		</div>
	<?php endforeach?>
<?php endif?>