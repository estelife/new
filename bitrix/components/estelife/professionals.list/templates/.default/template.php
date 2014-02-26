<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<div class="inner">
	<ul class="crumb">
		<li><a href="/">Главная</a></li>
		<li><b>Специалисты</b></li>
	</ul>
	<div class="title">
		<h1>Специалисты</h1>
	</div>
	<div class="items">
		<?php if (!empty($arResult['prof'])):?>
			<?php foreach ($arResult['prof'] as $arProf):?>
				<div class="item specialist">
					<div class="img">
						<div class="img-in">
							<?php if(!empty($arProf["logo"])): ?>
								<?=$arProf["logo"]?>
							<?php else: ?>
								<div class="default">Изображение отсутствует</div>
							<?endif?>
						</div>
					</div>
					<h2><a href="<?=$arProf["link"]?>"><?=$arProf["name"]?></a></h2>
					<?php if (!empty($arProf["country_name"])):?>
						<span class="country c<?=$arProf["country_id"]?>"><?=$arProf["country_name"]?></span>
					<?php endif?>
				</div>
			<?php endforeach?>
		<?php endif?>
	</div>
	<div class="not-found<?=(!empty($arResult['prof']) ? ' none' : '')?>">Специалисты не найдены ...</div>
	<?php if (!empty($arResult['nav'])):?>
		<?=$arResult['nav']?>
	<?php endif; ?>
</div>