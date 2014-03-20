<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?php if ($arResult['count']):?>
	<div class="experts">
		<div class="item-rel">
			<h2><?=$arParams['TITLE']?></h2>
			<?php foreach ($arResult['iblock'] as $key=>$val):?>
				<div class="item<?php if ($key>0):?> none<?php endif?>">
					<div class="user">
						<img src="<?=$val['IMG']?>" alt="<?=$val['NAME']?>" title="<?=$val['NAME']?>" width="146px" />
						<b><?=$val['AUTHOR']?></b>
						<i><?=$val['PROFESSION']?></i>
					</div>
					<div class="quote">
						<h3><a href="/ex<?=$val['ID']?>/"><?=$val['NAME']?></a></h3>
						<p><?=$val['PREVIEW_TEXT']?></p>
					</div>
				</div>
			<?php endforeach?>
			<ul class="menu">
				<?php for($i=0; $i<$arResult['count']; $i++): ?>
					<li<?=($i==0 ? ' class="active"' : '')?>><a href="#"><i></i></a></li>
				<?php endfor; ?>
			</ul>
		</div>
		<div class="border"></div>
	</div>
<?php endif?>