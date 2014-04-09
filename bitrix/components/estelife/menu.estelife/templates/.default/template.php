<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<ul class="menu main_menu">
	<li><a href="#" class="empty_link">Тема недели</a>
		<?php if (!empty($arResult['tz'])):?>
			<ul class="submenu">
				<?php foreach ($arResult['tz'] as $val):?>
					<li><a href="/podcast/<?=$val['CODE']?>/"><?=$val['NAME']?></a></li>
				<?php endforeach?>
			</ul>
		<?php endif?>
	</li>
	<li><a href="#" class="empty_link">Косметология</a>
		<ul class="submenu">
			<li><a href="/articles/krasivoe-lico/">Красивое лицо</a></li>
			<li><a href="/articles/idealnoe-telo/">Идеальное тело</a></li>
			<li><a href="/articles/izjashhnye-ruki/">Изящные руки</a></li>
			<li><a href="/articles/prekrasnye-nozhki/">Прекрасные ножки</a></li>
			<li><a href="/articles/raznoe/">Разное</a></li>
		</ul>
	</li>
	<li><a href="/clinics/">Клиники</a></li>
	<li><a href="/promotions/">Акции</a></li>
	<li><a href="#" class="empty_link">События</a>
		<ul class="submenu">
			<li><a href="/organizers/">Организаторы</a></li>
			<li><a href="/events/">Календарь событий</a></li>
		</ul>
	</li>
	<li><a href="#" class="empty_link">Справочник</a>
		<ul class="submenu">
			<li><a href="/preparations/">Препараты</a></li>
			<li><a href="/threads/">Нити</a></li>
			<li><a href="/implants/">Имплантаты</a></li>
			<li><a href="/apparatuses/">Аппараты</a></li>
			<li><a href="/preparations-makers/">Производители</a></li>
		</ul>
	</li>
	<?php if ($arResult['yvoire']==1):?>
		<li><a href="/yvoire/" class="no-ajax">Yvoire</a>
			<ul class="submenu">
				<li><a href="/yvoire/about/" class="no-ajax">О компании</a></li>
				<li><a href="/yvoire/products/" class="no-ajax">Продукция</a></li>
				<li><a href="/yvoire/opinions/" class="no-ajax">Мнение экспертов</a></li>
				<li><a href="/yvoire/events/" class="no-ajax">События</a></li>
			</ul>
		</li>
	<?php endif?>
</ul>

