<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?php if (!empty($arResult['iblock'])):?>
	<div class="experts">
		<div class="item-rel">
			<h2><?=$arParams['TITLE']?></h2>
			<?php foreach ($arResult['iblock'] as $key=>$val):?>
				<div class="item<?php if ($key>0):?> none<?php endif?>">
					<div class="user">
						<img src="<?=$val['IMG']?>" alt="<?=$val['NAME']?>" title="<?=$val['NAME']?>" width="190" />
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
                <?php foreach ($arResult['iblock'] as $key=>$val):?>
				    <li class="<?php if ($key==0):?> active<?php endif?>"><a href="#"><i></i></a></li>
                <?php endforeach?>
			</ul>
		</div>
		<div class="border"></div>
	</div>
<?php endif?>