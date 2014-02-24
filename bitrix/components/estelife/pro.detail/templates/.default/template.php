<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<div class="content">
	<div class="inner">
		<ul class="crumb">
			<li><a href="/">Главная</a></li>
			<li><b><?=$arResult["NAME"]?></b></li>
		</ul>
		<div class="item detail big-font">
			<h1><?=$arResult["NAME"]?></h1>
<!--			<ul class="stat notlike" data-elid="--><?//=$arResult['LIKES']['element_id']?><!--" data-type="--><?//=$arResult['LIKES']['type']?><!--">-->
<!--				--><?php //if (!empty($arResult['ACTIVE_FROM'])):?>
<!--					<li class="date">--><?//=date('d.m.Y',strtotime($arResult['ACTIVE_FROM']))?><!--</li>-->
<!--				--><?php //endif?>
<!--				<li class="likes islike">--><?//=$arResult['LIKES']['countLike']?><!----><?//if ($arResult['LIKES']['typeLike']==1):?><!-- и Ваш--><?//endif?><!--<i></i></li>-->
<!--				<li class="unlikes islike">--><?//=$arResult['LIKES']['countDislike']?><!----><?//if ($arResult['LIKES']['typeLike']==2):?><!-- и Ваш--><?//endif?><!--<i></i></li>-->
<!--			</ul>-->
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
<!--				<ul class="stat" data-elid="--><?//=$arResult['LIKES']['element_id']?><!--" data-type="--><?//=$arResult['LIKES']['type']?><!--">-->
<!--					<li><a href="#" class="likes islike--><?//if ($arResult['LIKES']['typeLike']==1):?><!-- active--><?//endif?><!--" data-help="Нравится">--><?//=$arResult['LIKES']['countLike']?><!----><?//if ($arResult['LIKES']['typeLike']==1):?><!-- и Ваш--><?//endif?><!--<i></i></a></li>-->
<!--					<li><a href="#" class="unlikes islike--><?//if ($arResult['LIKES']['typeLike']==2):?><!-- active--><?//endif?><!--" data-help="Не нравится">--><?//=$arResult['LIKES']['countDislike']?><!----><?//if ($arResult['LIKES']['typeLike']==2):?><!-- и Ваш--><?//endif?><!--<i></i></a></li>-->
<!--				</ul>-->
				<div class="social cols repost">
					<span>Поделиться: </span>
					<a href="http://vkontakte.ru/share.php?url=http://estelife.ru<?=$arResult['PATH']?>" target="_blank" class="vk">ВКонтакте</a>
					<a href="https://www.facebook.com/sharer.php?u=http://estelife.ru<?=$arResult['PATH']?>" target="_blank" class="fb">Facebook</a>
				</div>
			</div>
		</div>
		<div class="comments-ajax">
			<?$APPLICATION->IncludeComponent("estelife:comments.list",
				"",
				array(
					"element_id"=>$arResult["ID"],
					"type"=>$arParams["LINK_CODE"],
					"count"=>"5"
				)
			);?>
		</div>
	</div>
	<div class="adv adv-out right">
		<?$APPLICATION->IncludeComponent("bitrix:advertising.banner","",Array(
				"TYPE" => "main_right_1",
				"CACHE_TYPE" => "A",
				"NOINDEX" => "N",
				"CACHE_TIME" => "3600"
			)
		);?>
	</div>
</div>