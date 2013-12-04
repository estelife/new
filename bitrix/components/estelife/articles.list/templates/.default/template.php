<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?php if (!empty($arResult['iblock'])):?>
	<div class="articles ">
		<div class="title">
			<h2><?=$arParams['TITLE']?></h2>
			<a href="<?=$arResult['first_section']?>"><?=$arParams['MORE_TITLE']?></a>
		</div>
		<ul class="menu">
			<?php if (!empty($arResult['SECTIONS_NAME'])):?>
				<?php foreach ($arResult['SECTIONS_NAME'] as $key=>$val):?>
					<li class="<?php if ($arResult['first']==$key):?>active<?php endif?>"><a href="#"><span><?=$val?></span></a></li>
				<?php endforeach?>
			<?php endif;?>

		</ul>
		<?php foreach ($arResult['iblock'] as $key=>$arArticle):?>
			<div class="items <?php if ($arResult['first']!=$key):?>none<?php endif?>" rel="<?=$arArticle['section']?>">
				<?php foreach ($arArticle['articles'] as $key=>$val):?>
					<div class="item">
						<img src="<?=$val['IMG']['SRC']?>" alt="<?=$val['NAME']?>" title="<?=$val['NAME']?>" width="229px" height="160px" />
						<h3><a href="<?=$val['DETAIL_URL']?>"><?=$val['NAME']?></a></h3>
						<p><?=$val['PREVIEW_TEXT']?></p>
						<ul class="stat">
							<li class="date"><?=$val['ACTIVE_FROM']?></li>
	<!--						<li class="comments">9<i></i></li>-->
	<!--						<li class="likes">41<i></i></li>-->
	<!--						<li class="unlikes">2<i></i></li>-->
						</ul>
					</div>
				<?php endforeach?>
			</div>
		<?php endforeach?>
	</div>
<?php endif?>