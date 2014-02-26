<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
?>

<ul class="crumb">
	<li><a href="/">Главная</a></li>
	<li><a href="/events/">Календарь событий</a></li>
	<li><a href="/ev<?=$arResult['event']['id']?>/"><?=$arResult['event']['name']?></a></li>
	<li><b>Программа мероприятия</b></li>
</ul>

<div class="activity">
	<h1>Программа мероприятия</h1>
	<h2><?=$arResult['event']['name']?></h2>

	<ul class="dates">
		<?php foreach($arResult['dates'] as $arDate): ?>
			<li>
				<a href="/ev<?=$arResult['event']['id']?>/program/?date=<?=$arDate['date']?>"<?=($arDate['date']==$arResult['current_date'] ? ' class="active"' : '')?>>
					<span><?=$arDate['day']?></span> <?=$arDate['month']?>
					<i></i>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>

	<div class="items">
		<?php foreach($arResult['halls'] as $arHall): ?>
			<?php if(!empty($arHall['activities'])): ?>
				<div class="item">
					<h3><a href="#"><?=$arHall['name']?></a></h3>
					<div class="item-in">
							<ul>
								<?php foreach($arHall['activities'] as $arActivity): ?>
									<li class="<?=($arActivity['group']==1 ? 'group' : 'one')?>">
										<?php if(!empty($arActivity['time'])): ?>
											<span><?=$arActivity['time']?></span>
										<?php endif; ?>
										<p><?=$arActivity['name']?></p>
									</li>
								<?php endforeach; ?>
							</ul>
					</div>
				</div>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
</div>