<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="el-ajax-detail">
	<div class="block" rel="pill">
		<div class='block-header red'>
			<span><?=$arResult['pill']['name']?></span>
		</div>
		<div class='shadow'></div>
		<div class="el-ditem el-ditem-h">
			<div class="logo el-col">
				<?=$arResult['pill']['img']?>
			</div>
			<div class="el-scroll">
					<div class="el-scroll-in">
						<table>
							<tr>
								<td>
									<ul class="contacts el-col el-ul el-contacts">
										<?php if (!empty($arResult['pill']['country_name'])):?>
											<li><span>Страна</span><span><?=$arResult['pill']['country_name']?></span><i class="icon" style="background:url('/img/countries/c<?=$arResult['pill']['country_id']?>.png')"></i></li>
										<?php endif?>
										<?php if (!empty($arResult['pill']['company_name'])):?>
										<li><span>Компания</span><span><a href="<?=$arResult['pill']['company_link']?>"><?=$arResult['pill']['company_name']?></a></span></li>
										<?php endif?>
										<?php if (!empty($arResult['pill']['type_name'])):?>
											<li><span>Вид препарата</span><span><?=$arResult['pill']['type_name']?></span></li>
										<?php endif?>
									</ul>
								</td>
							</tr>
						</table>
					</div>
			</div>
			<div class="clear"></div>
			<?php if (!empty($arResult['types'])):?>
				<div class="el-prop">
					<h3>Типы препарата</h3>
					<ul class="el-ul">
						<?php foreach ($arResult['types'] as $val):?>
							<li><?=$val?></li>
						<?php endforeach?>
					</ul>
				</div>
			<?php endif?>
			<div class="el-tab">
				<div>Описание<span class="open"></span></div>
				<p><?=$arResult['pill']['detail_text']?></p>
			</div>

			<?php if (!empty($arResult['pill']['registration'])):?>
				<div class="el-tab">
					<div>Регистрация<span class="close"></span></div>
					<p class="none"><?=$arResult['pill']['registration']?></p>
				</div>
			<?php endif?>
			<?php if (!empty($arResult['pill']['action'])):?>
				<div class="el-tab">
					<div>Действие<span class="close"></span></div>
					<p class="none"><?=$arResult['pill']['action']?></p>
				</div>
			<?php endif?>
			<?php if (!empty($arResult['pill']['undesired'])):?>
				<div class="el-tab">
					<div>Побочные действия<span class="close"></span></div>
					<p class="none"><?=$arResult['pill']['undesired']?></p>
				</div>
			<?php endif?>
			<?php if (!empty($arResult['pill']['evidence'])):?>
				<div class="el-tab">
					<div>Показания<span class="close"></span></div>
					<p class="none"><?=$arResult['pill']['evidence']?></p>
				</div>
			<?php endif?>
			<?php if (!empty($arResult['pill']['structure'])):?>
				<div class="el-tab">
					<div>Состав<span class="close"></span></div>
					<p class="none"><?=$arResult['pill']['structure']?></p>
				</div>
			<?php endif?>
			<?php if (!empty($arResult['pill']['effect'])):?>
				<div class="el-tab">
					<div>Достигаемый эффект<span class="close"></span></div>
					<p class="none"><?=$arResult['pill']['effect']?></p>
				</div>
			<?php endif?>
			<?php if (!empty($arResult['pill']['form'])):?>
				<div class="el-tab">
					<div>Форма выпуска<span class="close"></span></div>
					<p class="none"><?=$arResult['pill']['form']?></p>
				</div>
			<?php endif?>
			<?php if (!empty($arResult['pill']['contra'])):?>
				<div class="el-tab">
					<div>Противопоказания<span class="close"></span></div>
					<p class="none"><?=$arResult['pill']['contra']?></p>
				</div>
			<?php endif?>
			<?php if (!empty($arResult['pill']['usage'])):?>
				<div class="el-tab">
					<div>Способ применения<span class="close"></span></div>
					<p class="none"><?=$arResult['pill']['usage']?></p>
				</div>
			<?php endif?>
			<?php if (!empty($arResult['pill']['storage'])):?>
				<div class="el-tab">
					<div>Условия хранения<span class="close"></span></div>
					<p class="none"><?=$arResult['pill']['storage']?></p>
				</div>
			<?php endif?>
			<?php if (!empty($arResult['pill']['advantages'])):?>
				<div class="el-tab">
					<div>Преимущества<span class="close"></span></div>
					<p class="none"><?=$arResult['pill']['advantages']?></p>
				</div>
			<?php endif?>
			<?php if (!empty($arResult['pill']['area'])):?>
				<div class="el-tab">
					<div>Зоны применения<span class="close"></span></div>
					<p class="none"><?=$arResult['pill']['area']?></p>
				</div>
			<?php endif?>
			<?php if (!empty($arResult['pill']['security'])):?>
				<div class="el-tab">
					<div>Безопасность<span class="close"></span></div>
					<p class="none"><?=$arResult['pill']['security']?></p>
				</div>
			<?php endif?>
			<?php if (!empty($arResult['pill']['mix'])):?>
				<div class="el-tab">
					<div>Сочетание<span class="close"></span></div>
					<p class="none"><?=$arResult['pill']['mix']?></p>
				</div>
			<?php endif?>
			<?php if (!empty($arResult['pill']['protocol'])):?>
				<div class="el-tab">
					<div>Протокол процедуры<span class="close"></span></div>
					<p class="none"><?=$arResult['pill']['protocol']?></p>
				</div>
			<?php endif?>
			<?php if (!empty($arResult['pill']['specs'])):?>
				<div class="el-tab">
					<div>Технические характеристики<span class="close"></span></div>
					<p class="none"><?=$arResult['pill']['specs']?></p>
				</div>
			<?php endif?>

		</div>
	</div>
	<?php if (!empty($arResult['pill']['gallery'])):?>
		<div class="block" rel="pill">
			<div class='block-header blue'>
				<span>Фотографии до/после</span>
			</div>
			<div class="dl_item">
				<div class="el-gallery">
					<?php foreach ($arResult['pill']['gallery'] as $val):?>
						<div class="image">
							<a href="<?=$val?>" class="colorbox" rel="pill" target="_blank" title="">
								<img src="<?=$val?>" alt="" title="" />
								<span class="desc"></span>
							</a>
						</div>
					<?php endforeach?>
				</div>
			</div>
		</div>
	<?php endif?>
	<?php if (!empty($arResult['pill']['production'])):?>
		<div class="block" rel="clinic">
			<div class='block-header blue'>
				<span>Другие препараты <?=$arResult['pill']['company_name']?></span>
			</div>
			<div class="el-ditem-action production-events production" >
				<?php foreach ($arResult['pill']['production'] as $arValue):?>
					<div class="section big">
						<a href="<?=$arValue["link"]?>">
							<div class="h"><?=$arValue["name"]?></div>
						</a>
						<div class="i">
							<?php if(!empty($arValue["logo_id"])):?>
								<?=$arValue["img"]?>
							<?endif?>
						</div>

						<div class="t"><?=$arValue["preview_text"]?></div>
					</div>
				<?php endforeach?>
				<div class="clear"></div>
			</div>
		</div>
	<?php endif?>
</div>
<div class="block none" rel="news">
	<div class="block-header blue">
		<h1><?=GetMessage("ESTELIFE_PILL")?></h1>
		<div class="clear"></div>
	</div>
	<div class="shadow"></div>
	<div class="tab-group" rel="articles">
		<div class="panel">
			<div class="el-items"></div>
			<div class="el-not-found none"><?=GetMessage("ESTELIFE_PILL_NOT_FOUND")?></div>
			<div class="clear"></div>
			<div class="pagination"></div>
		</div>
	</div>
</div>