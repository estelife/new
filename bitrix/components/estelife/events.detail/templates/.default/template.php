<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="el-ajax-detail">
	<div class="block" rel="event">
		<div class='block-header red'>
			<h1><?=$arResult['event']['short_name']?></h1>
		</div>
		<div class='shadow'></div>
		<div class="el-ditem el-ditem-h">
			<div class="logo el-col">
				<?php if (!empty ($arResult['event']['logo_id'])):?>
					<?=$arResult['event']['img']?>
				<?php endif?>
			</div>
			<div class="el-scroll">
				<h2><?=$arResult['event']['full_name']?></h2>
				<div class="el-scroll-in">
					<table>
						<tr>
							<td>
								<ul class="contacts el-col el-ul el-contacts">
									<?php if (!empty($arResult['event']['country_name'])):?>
										<li><span>Страна</span><span><?=$arResult['event']['country_name']?></span><i class="icon" style="background:url('/img/countries/c<?=$arResult['event']['country_id']?>.png')"></i></li>
									<?php endif?>
									<?php if (!empty($arResult['event']['city_name'])):?>
									<li><span>Город</span><span><?=$arResult['event']['city_name']?></span></li>
									<?php endif?>
									<?php if (!empty($arResult['event']['web'])):?>
										<li><span>Сайт</span><span><a href="<?=$arResult['event']['web']?>"><?=$arResult['event']['short_web']?></a></span></li>
									<?php endif?>
								</ul>
							</td>
						</tr>
					</table>
				</div>
			</div>
			<div class="clear"></div>

			<?php if (!empty($arResult['event']['calendar'])):?>
				<div class="el-prop">
					<h3>Даты проведения</h3>
					<ul class="el-ul">
						<?php foreach ($arResult['event']['calendar'] as $val):?>
							<li><?=$val['full_date']?></li>
						<?php endforeach?>
					</ul>
				</div>
			<?php endif?>

			<h3>Место проведения</h3>
			<?php if (!empty($arResult['event']['dop_address'])):?>
				<p><?=$arResult['event']['dop_address']?></p>
			<?php endif?>
			<?php if (!empty($arResult['event']['dop_web'])):?>
				<p><a href="<?=$arResult['event']['dop_web']?>"><?=$arResult['event']['short_dop_web']?></a></p>
			<?php endif?>
			<?php if (!empty($arResult['event']['address'])):?>
				<p><?=$arResult['event']['address']?></p>
			<?php endif?>

			<?php if (!empty($arResult['event']['org'])):?>
				<div class="el-prop">
					<h3>Организаторы</h3>
					<ul class="el-ul">
						<?php foreach ($arResult['event']['org'] as $val):?>
							<li><?=$val['company_name']?></li>
						<?php endforeach?>
					</ul>
				</div>
			<?php endif?>
			<h3>Описание</h3>
			<p><?=$arResult['event']['detail_text']?></p>
			<?php if (!empty($arResult['event']['contacts'])):?>
				<h3>Контактные данные</h3>
				<div class="el-table">
					<table>
						<?php if (!empty($arResult['event']['main_org'])):?>
							<tr>
								<td class="t">Организация:</td>
								<td class="d">
									<?=$arResult['event']['main_org']['company_name']?>
								</td>
							</tr>
						<?php endif?>
						<?php if (!empty($arResult['event']['main_org']['full_address'])):?>
							<tr>
								<td class="t">Адрес:</td>
								<td class="d">
									<?=$arResult['event']['main_org']['full_address']?>
								</td>
							</tr>
						<?php endif?>
						<?php if (!empty($arResult['event']['contacts']['phone'])):?>
							<tr>
								<td class="t">Телефон:</td>
								<td class="d">
									<?=$arResult['event']['contacts']['phone']?>
								</td>
							</tr>
						<?php endif?>
						<?php if (!empty($arResult['event']['contacts']['fax'])):?>
							<tr>
								<td class="t">Факс:</td>
								<td class="d">
									<?=$arResult['event']['contacts']['fax']?>
								</td>
							</tr>
						<?php endif?>
						<?php if (!empty($arResult['event']['contacts']['email'])):?>
							<tr>
								<td class="t">E-mail:</td>
								<td class="d">
									<?=$arResult['event']['contacts']['email']?>
								</td>
							</tr>
						<?php endif?>
						<?php if (!empty($arResult['event']['main_org']['web'])):?>
							<tr>
								<td class="t">Официальный сайт:</td>
								<td class="d">
									<a href="<?=$arResult['event']['main_org']['web']?>"><?=$arResult['event']['main_org']['short_web']?></a>
								</td>
							</tr>
						<?php endif?>
					</table>
				</div>
			<?php endif?>
		</div>
	</div>
</div>
<div class="block none" rel="news">
	<div class="block-header red">
		<h1><?=GetMessage("ESTELIFE_EVENTS")?></h1>
		<div class="clear"></div>
	</div>
	<div class="shadow"></div>
	<div class="tab-group" rel="articles">
		<div class="el-items"></div>
		<div class="el-not-found none"><?=GetMessage('ESTELIFE_EVENTS_NOT_FOUND')?></div>
		<div class="clear"></div>
		<div class="pagination"></div>
	</div>
</div>