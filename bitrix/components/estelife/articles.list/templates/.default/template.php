<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?php if (!empty($arResult['iblock'])):?>
	<div class="articles ">
		<div class="title">
			<h2><?=$arParams['TITLE']?></h2>
			<ul class="tabs-menu">
				<?php if (!empty($arResult['SECTIONS_NAME'])):?>
					<?php $i=1;?>
					<?php foreach ($arResult['SECTIONS_NAME'] as $key=>$val):?>
						<li<?php if ($i==1):?> class="active"<?php endif?>><a href="#"><span><?=$val?></span><i></i></a></li>
						<?php $i++?>
					<?php endforeach?>
				<?php endif;?>

			</ul>
		</div>
		<?php foreach ($arResult['iblock'] as $key=>$arArticle):?>
			<div class="items <?php if ($arResult['first']!=$key):?>none<?php endif?>" rel="<?=$arArticle['section']?>">
				<?php foreach ($arArticle['articles'] as $key=>$val):?>
					<div class="item article">
						<div class="item-in">
							<img src="<?=$val['IMG']?>" alt="<?=$val['NAME']?>" title="<?=$val['NAME']?>" />
							<h3><a href="<?=$val['DETAIL_URL']?>"><?=$val['NAME']?></a></h3>
							<p><?=$val['PREVIEW_TEXT']?></p>
						</div>
						<ul class="stat">
							<li class="date"><?=$val['ACTIVE_FROM']?></li>
							<li class="likes"><?if ($val['LIKES']['countLike']>0):?><?=$val['LIKES']['countLike']?><?else:?>0<?endif?><i></i></li>
							<li class="unlikes"><?if ($val['LIKES']['countDislike']>0):?><?=$val['LIKES']['countDislike']?><?else:?>0<?endif?><i></i></li>
						</ul>
					</div>
				<?php endforeach?>
			</div>
		<?php endforeach?>
	</div>
<?php endif?>