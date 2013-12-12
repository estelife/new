<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="inner">
	<ul class="crumb">
		<li><a href="/">Главная</a></li>
		<li><a href="/">Учебные центры</a></li>
		<li><b><?=$arResult['company']['name']?></b></li>
	</ul>
	<div class="item detail company center">
		<h1><?=$arResult['company']['name']?></h1>
		<div class="img">
			<div class="img-in">
				<?if (!empty($arResult['company']['img'])):?>
					<?=$arResult['company']['img']?>
				<?php endif?>
			</div>
		</div>
		<div class="cols col1">
			<?php if (!empty($arResult['company']['address'])):?>
				<span><?=$arResult['company']['address']?></span>
			<?php endif?>
			<?php if (!empty($arResult['company']['contacts']['phone'])):?>
				<span><?=$arResult['company']['contacts']['phone']?></span>
			<?php endif?>
			<?php if (!empty($arResult['company']['web'])):?>
				<a href="<?=$arResult['company']['web']?>"><?=$arResult['company']['web_short']?></a>
			<?php endif?>
		</div>
		<div class="menu menu_tab">
			<ul>
				<li class="active t1"><a href="#"><span>О центре</span></a></li>
				<li class="t2"><a href="#"><span>Текущие семинары</span></a></li>
				<li class="t3"><a href="#"><span>Контакты</span></a></li>
			</ul>
		</div>
		<div class="tabs tab1 ">
			<p><?=$arResult['company']['detail_text']?></p>
		</div>
		<div class="tabs tab2 none">
			<h2>Декабрь</h2>
			<div class="items">
				<div class="item training">
					<h2><a href="#">3D Мезонити</a></h2>
					<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus ac sapien sapien. Phasellus enim lectus, pharetra in lacus et, tempus iaculis dui.</p>
					Период проведения: <b>3 декабря</b><br>
					<span class="date">3 <i>дек</i></span>
				</div>
			</div>
			<div class="items">
				<div class="item training">
					<h2><a href="#">3D Мезонити</a></h2>
					<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus ac sapien sapien. Phasellus enim lectus, pharetra in lacus et, tempus iaculis dui.</p>
					Период проведения: <b>3 декабря</b><br>
					<span class="date">3 <i>дек</i></span>
				</div>
			</div>
			<div class="items">
				<div class="item training">
					<h2><a href="#">3D Мезонити</a></h2>
					<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus ac sapien sapien. Phasellus enim lectus, pharetra in lacus et, tempus iaculis dui.</p>
					Период проведения: <b>3 декабря</b><br>
					<span class="date">3 <i>дек</i></span>
				</div>
			</div>
			<div class="items">
				<div class="item training">
					<h2><a href="#">3D Мезонити</a></h2>
					<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus ac sapien sapien. Phasellus enim lectus, pharetra in lacus et, tempus iaculis dui.</p>
					Период проведения: <b>3 декабря</b><br>
					<span class="date">3 <i>дек</i></span>
				</div>
			</div>
		</div>
		<div class="tab-c tabs tab3 none">
			<ul>
				<li>
					<b>Адрес</b>
					<span>м. Лиговский проспект, ул. Черняховского, д. 53</span>
				</li>
				<li>
					<b>Телефон</b>
					<span>+7 (812) 777-03-60</span>
				</li>
				<li>
					<b>Режим работы</b>
					<span>с 9:00 до 21:00 <i>без выходных</i></span>
				</li>
				<li>
					<b>Сайт клиники</b>
					<a href="#">www.gruzdevclinic.ru</a>
				</li>
				<li>
					<b>Принимают к оплате</b>
					<span>наличные, банковские карты, кредит</span>
				</li>
			</ul>
			<div class="map">

			</div>
		</div>
	</div>
</div>
<!--
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
-->