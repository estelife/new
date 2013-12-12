<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?php if (!empty($arResult['iblock'])):?>
	<div class="experts">
		<h2><?=$arParams['TITLE']?></h2>
		<?php foreach ($arResult['iblock'] as $key=>$val):?>
			<div class="item <?php if ($key>0):?>none<?php endif?>">
				<div class="user">
					<img src="<?=$val['IMG']['SRC']?>" alt="<?=$val['NAME']?>" title="<?=$val['NAME']?>" width="146px" height="100px" />
					<b><?=$val['AUTHOR']?></b>
					<i><?=$val['PROFESSION']?></i>
				</div>
				<div class="quote">
					<h3><?=$val['NAME']?></h3>
					<p><?=$val['PREVIEW_TEXT']?></p>
				</div>
			</div>
		<?php endforeach?>
		<ul class="menu">
			<li class="active"><a href="#"><i></i></a></li>
			<li><a href="#"><i></i></a></li>
			<li><a href="#"><i></i></a></li>
		</ul>
	</div>
<?php endif?>