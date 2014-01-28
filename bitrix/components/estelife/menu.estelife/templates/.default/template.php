<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<ul class="menu main_menu">
	<li><a href="#" class="empty_link">Точка зрения</a>
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
	<li><a href="/clinics/" title="Клиники">Клиники</a></li>
	<li><a href="/promotions/" title="Акции">Акции</a></li>
	<li><a href="#" class="empty_link">События</a>
		<ul class="submenu">
			<li><a href="/sponsors/" title="Организаторы">Организаторы</a></li>
			<li><a href="/events/" title="Календарь событий">Календарь событий</a></li>
		</ul>
	</li>
	<li><a href="#" class="empty_link">Семинары</a>
		<ul class="submenu">
			<li><a href="/training-centers/" title="Учебые центры">Учебные центры</a></li>
			<li><a href="/trainings/" title="Расписание обучений">Расписание семинаров</a></li>
		</ul>
	</li>
	<li><a href="#" class="empty_link">Справочник</a>
		<ul class="submenu">
			<li><a href="/preparations-makers/" title="Производители">Производители</a></li>
			<li><a href="/preparations/" title="Препараты">Препараты</a></li>
			<li><a href="/apparatuses/" title="Аппараты">Аппараты</a></li>
		</ul>
	</li>
	<?php if ($arResult['yvoire']==1):?>
		<li class="last"><a href="/yvoire/" class="no-ajax">Yvoire</a>
			<ul class="submenu">
				<li><a href="/yvoire/about/" class="no-ajax" title="О компании">О компании</a></li>
				<li><a href="/yvoire/products/" class="no-ajax" title="Продукция">Продукция</a></li>
				<li><a href="/yvoire/opinions/" class="no-ajax" title="Мнение экспертов">Мнение экспертов</a></li>
				<li><a href="/yvoire/events/" class="no-ajax" title="События">События</a></li>
			</ul>
		</li>
	<?php endif?>
</ul>