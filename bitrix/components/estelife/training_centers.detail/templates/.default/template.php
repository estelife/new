<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="el-ajax-detail">
	<div class="block el-block" rel="clinic">
		<div class='block-header red'>
			<span><?=$arResult['company']['name']?></span>
			<div class='clear'></div>
			<div class='shadow'></div>
		</div>
		<div class="el-ditem el-ditem-h">
			<div class="logo el-col">
				<?if (!empty($arResult['company']['img'])):?>
					<?=$arResult['company']['img']?>
				<?php endif?>
			</div>
			<div class="el-scroll">
				<div class="el-scroll-in">
					<table><tr>
						<td>
							<ul class="contacts el-col el-ul el-contacts">
								<?php if (!empty($arResult['company']['country_name'])):?>
									<li><span>Страна</span><span><?=$arResult['company']['country_name']?></span><i class="icon" style="background:url('/img/countries/c<?=$arResult['company']['country_id']?>.png')"></i></li>
								<?php endif?>
							</ul>
							<ul class="contacts el-col el-ul el-contacts">
								<?php if (!empty($arResult['company']['city_name'])):?>
									<li><span>Город</span><span><?=$arResult['company']['city_name']?></span></li>
								<?php endif?>
							</ul>
						</td>
					</tr></table>
				</div>
			</div>
			<h3>О компании</h3>
			<p><?=$arResult['company']['detail_text']?></p>
			<h3>Контактные данные</h3>
			<div class="el-table">
				<table>
					<?php if (!empty($arResult['company']['address'])):?>
						<tr>
							<td class="t">Адрес:</td>
							<td class="d">
								<?=$arResult['company']['address']?>
							</td>
						</tr>
					<?php endif?>
					<?php if (!empty($arResult['company']['contacts']['phone'])):?>
						<tr>
							<td class="t">Телефон:</td>
							<td class="d">
								<?=$arResult['company']['contacts']['phone']?>
							</td>
						</tr>
					<?php endif?>
					<?php if (!empty($arResult['company']['contacts']['fax'])):?>
						<tr>
							<td class="t">Факс:</td>
							<td class="d">
								<?=$arResult['company']['contacts']['fax']?>
							</td>
						</tr>
					<?php endif?>
					<?php if (!empty($arResult['company']['contacts']['email'])):?>
						<tr>
							<td class="t">E-mail:</td>
							<td class="d">
								<?=$arResult['company']['contacts']['email']?>
							</td>
						</tr>
					<?php endif?>
					<?php if (!empty($arResult['company']['contacts']['web'])):?>
						<tr>
							<td class="t">Официальный сайт:</td>
							<td class="d">
								<a href="<?=$arResult['company']['contacts']['web']?>"><?=$arResult['company']['contacts']['web_short']?></a>
							</td>
						</tr>
					<?php endif?>
				</table>
			</div>
		</div>
	</div>
</div>
<div class="block none" rel="news">
	<div class="block-header blue">
		<h1><?=GetMessage("ESTELIFE_COMPANY")?></h1>
		<div class="clear"></div>
	</div>
	<div class="shadow"></div>
	<div class="tab-group" rel="articles">
		<div class="panel">
			<div class="el-items"></div>
			<div class="el-not-found none"><?=GetMessage("ESTELIFE_COMPANY_NOT_FOUND")?></div>
			<div class="clear"></div>
			<div class="pagination"></div>
		</div>
	</div>
</div>