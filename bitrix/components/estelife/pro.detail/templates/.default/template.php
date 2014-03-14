<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<ul class="crumb">
	<li><a href="/" class="no-ajax">Главная</a></li>
	<li><a href="/pro/" class="no-ajax">Библиотека специалиста</a></li>
	<li><b><?=$arResult["NAME"]?></b></li>
</ul>
<div class="item detail">
	<h1><?=$arResult["NAME"]?></h1>
	<div class="announce">
		<?=$arResult["PREVIEW_TEXT"];?>
	</div>
	<?php if(!empty($arResult['IMG']['SRC'])): ?>
		<div class="article-img">
			<div class="article-img-in">
				<img src="<?=$arResult['IMG']['SRC']?>" alt="<?=$arResult["NAME"]?>" title="<?=$arResult["NAME"]?>">
			</div>
			<?php if (!empty($arResult['IMG']['DESCRIPTION'])):?>
				<div class="article-img-desc">
					<?=$arResult['IMG']['DESCRIPTION']?>
				</div>
			<?php endif?>
		</div>
	<?php endif; ?>
	<?=$arResult["DETAIL_TEXT"];?>
	<div class="info">
		<div class="social cols repost">
			<span>Поделиться: </span>
			<a href="http://vkontakte.ru/share.php?url=http://estelife.ru<?=$arResult['PATH']?>" target="_blank" class="vk">ВКонтакте</a>
			<a href="https://www.facebook.com/sharer.php?u=http://estelife.ru<?=$arResult['PATH']?>" target="_blank" class="fb">Facebook</a>
		</div>
	</div>
</div>