<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="el-ajax-detail">
	<div class="block" rel="app">
		<div class='block-header red'>
			<span><?=$arResult['app']['name']?></span>
		</div>
		<div class='shadow'></div>
		<div class="el-ditem el-ditem-h">
			<div class="logo el-col">
				<?=$arResult['app']['img']?>
			</div>
			<div class="el-scroll">
				<div class="el-scroll-in">
					<table>
						<tr>
							<td>
								<ul class="contacts el-col el-ul el-contacts">
									<?php if (!empty($arResult['app']['country_name'])):?>
										<li><span>Страна</span><span><?=$arResult['app']['country_name']?></span><i class="icon" style="background:url('/img/countries/c<?=$arResult['app']['country_id']?>.png')"></i></li>
									<?php endif?>
									<?php if (!empty($arResult['app']['company_name'])):?>
										<li><span>Компания</span><span><a href="<?=$arResult['app']['company_link']?>"><?=$arResult['app']['company_name']?></a></span></li>
									<?php endif?>
									<?php if (!empty($arResult['app']['type_name'])):?>
										<li><span>Вид препарата</span><span><?=$arResult['app']['type_name']?></span></li>
									<?php endif?>
								</ul>
							</td>
						</tr>
					</table>
				</div>
			</div>
			<div class="clear"></div>
			<?php if (!empty($arResult['app']['types'])):?>
				<div class="el-prop">
					<h3>Типы аппаратов</h3>
					<ul class="el-ul">
						<?php foreach ($arResult['app']['types'] as $val):?>
							<li><?=$val?></li>
						<?php endforeach?>
					</ul>
				</div>
			<?php endif?>
			<div class="el-tab">
				<div>Описание<span class="open"></span></div>
				<p><?=$arResult['app']['detail_text']?></p>
			</div>

			<?php if (!empty($arResult['app']['registration'])):?>
				<div class="el-tab">
					<div>Регистрация<span class="close"></span></div>
					<p class="none"><?=$arResult['app']['registration']?></p>
				</div>
			<?php endif?>
			<?php if (!empty($arResult['app']['action'])):?>
				<div class="el-tab">
					<div>Действие<span class="close"></span></div>
					<p class="none"><?=$arResult['app']['action']?></p>
				</div>
			<?php endif?>
			<?php if (!empty($arResult['app']['func'])):?>
				<div class="el-tab">
					<div>Функции<span class="close"></span></div>
					<p class="none"><?=$arResult['app']['func']?></p>
				</div>
			<?php endif?>
			<?php if (!empty($arResult['app']['undesired'])):?>
				<div class="el-tab">
					<div>Побочные действие<span class="close"></span></div>
					<p class="none"><?=$arResult['app']['undesired']?></p>
				</div>
			<?php endif?>
			<?php if (!empty($arResult['app']['evidence'])):?>
				<div class="el-tab">
					<div>Показания<span class="close"></span></div>
					<p class="none"><?=$arResult['app']['evidence']?></p>
				</div>
			<?php endif?>
			<?php if (!empty($arResult['app']['procedure'])):?>
				<div class="el-tab">
					<div>Курс процедур<span class="close"></span></div>
					<p class="none"><?=$arResult['app']['procedure']?></p>
				</div>
			<?php endif?>
			<?php if (!empty($arResult['app']['contra'])):?>
				<div class="el-tab">
					<div>Противопоказания<span class="close"></span></div>
					<p class="none"><?=$arResult['app']['contra']?></p>
				</div>
			<?php endif?>
			<?php if (!empty($arResult['app']['advantages'])):?>
				<div class="el-tab">
					<div>Преимущества<span class="close"></span></div>
					<p class="none"><?=$arResult['app']['advantages']?></p>
				</div>
			<?php endif?>
			<?php if (!empty($arResult['app']['security'])):?>
				<div class="el-tab">
					<div>Безопасность<span class="close"></span></div>
					<p class="none"><?=$arResult['app']['security']?></p>
				</div>
			<?php endif?>
			<?php if (!empty($arResult['app']['protocol'])):?>
				<div class="el-tab">
					<div>Протокол процедуры<span class="close"></span></div>
					<p class="none"><?=$arResult['app']['protocol']?></p>
				</div>
			<?php endif?>
			<?php if (!empty($arResult['app']['specs'])):?>
				<div class="el-tab">
					<div>Технические характеристики<span class="close"></span></div>
					<p class="none"><?=$arResult['app']['specs']?></p>
				</div>
			<?php endif?>
			<?php if (!empty($arResult['app']['equipment'])):?>
				<div class="el-tab">
					<div>Комплектация<span class="close"></span></div>
					<p class="none"><?=$arResult['app']['equipment']?></p>
				</div>
			<?php endif?>

		</div>
	</div>
<?php if (!empty($arResult['app']['gallery'])):?>
	<div class="block" rel="app">
		<div class='block-header blue'>
			<span>Фотографии до/после</span>
		</div>
		<div class="dl_item">
			<div class="el-gallery">
				<?php foreach ($arResult['app']['gallery'] as $val):?>
					<div class="image">
						<a href="<?=$val?>" class="colorbox" rel="app" target="_blank" title="">
							<img src="<?=$val?>" alt="" title="" />
							<span class="desc"></span>
						</a>
					</div>
				<?php endforeach?>
			</div>
		</div>
	</div>
<?php endif?>
<?php if (!empty($arResult['app']['production'])):?>
	<div class="block" rel="clinic">
		<div class='block-header blue'>
			<span>Другие аппараты <?=$arResult['app']['company_name']?></span>
		</div>
		<div class="el-ditem-action production-events production" >
			<?php foreach ($arResult['app']['production'] as $arValue):?>
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
	<div class="block-header red">
		<h1><?=GetMessage("ESTELIFE_APPS")?></h1>
		<div class="clear"></div>
	</div>
	<div class="shadow"></div>
	<div class="tab-group" rel="articles">
		<div class="el-items"></div>
		<div class="el-not-found none"><?=GetMessage('ESTELIFE_APPS_NOT_FOUND')?></div>
		<div class="clear"></div>
		<div class="pagination"></div>
	</div>
</div>