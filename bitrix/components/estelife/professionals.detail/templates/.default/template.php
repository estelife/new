<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<div class="inner">
	<ul class="crumb">
		<li><a href="/">Главная</a></li>
		<li><a href="/professionals/">Специалисты</a></li>
		<li><b><?=$arResult['professional']['name']?></b></li>
	</ul>
	<div class="wrap_item">
		<div class="item detail specialist">
			<div class="current">
				<div class="img">
					<div class="img-in">
						<?php if(!empty($arResult['professional']['img'])): ?>
							<?=$arResult['professional']['img']?>
						<?php else: ?>
							<div class="default">Изображение отсутствует</div>
						<?endif?>
					</div>
				</div>
				<span class="country c<?=$arResult['professional']['country_id']?>">Страна: <?=$arResult['professional']['country_name']?></span>
				<?php if (!empty($arResult['professional']['clinics'])):?>
					<div class="work">
						<h2>Место работы:</h2>
						<ul>
							<?php foreach ($arResult['professional']['clinics'] as $val):?>
								<li><a href="<?=$val['link']?>"><?=$val['name']?><i></i></a></li>
							<?php endforeach?>
						</ul>
					</div>
				<?php endif?>
			</div>
			<h1><?=$arResult['professional']['name']?></h1>
			<div class="about">
				<p><?=$arResult['professional']['full_description']?></p>
			</div>
			<div class="cl"></div>
			<?php if (!empty($arResult['professional']['activities'])):?>
				<h2>Участие в общественных мероприятиях</h2>
				<table>
					<col width="101">
					<col width="409">
					<tr>
						<th>Дата</th>
						<th>Тема доклада</th>
						<th>Место</th>
					</tr>
					<?php foreach ($arResult['professional']['activities'] as $val):?>
						<tr>
							<td><?=$val['date']?></td>
							<td>
								<?=$val['description']?>
							</td>
							<td>
								<a href="<?=$val['link_event']?>"><?=$val['event_name']?></a>
							</td>
						</tr>
					<?php endforeach?>
				</table>
			<?php endif?>
		</div>
	</div>
</div>