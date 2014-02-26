<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
?>

<ul class="crumb">
	<li><a href="/">Главная</a></li>
	<li><a href="/events/">Календарь событий</a></li>
	<li><a href="/ev<?=$arResult['event']['id']?>/"><?=$arResult['event']['short_name']?></a></li>
	<li><b>Программа мероприятия</b></li>
</ul>

<div class="activity">
	<h1>Программа мероприятия</h1>
	<h2><?=$arResult['event']['short_name']?></h2>

	<ul class="dates">
		<?php foreach($arResult['dates'] as $arDate): ?>
			<li>
				<a href="/ev<?=$arResult['event']['id']?>/program/?date=<?=$arDate['format']?>"<?=($arDate['date']==$arResult['current']['date'] ? ' class="active"' : '')?>>
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
					<div class="item-rel">
						<h3><a href="/ev<?=$arResult['event']['id']?>/<?=$arHall['translit']?>-<?=$arResult['current']['format']?>/"><?=$arHall['name']?></a></h3>
						<div class="item-in">
								<ul>
									<?php foreach($arHall['activities'] as $arActivity): ?>
										<li class="<?=($arActivity['group']==1 ? 'group' : 'one')?>">
											<?php if(!empty($arActivity['time'])): ?>
												<span><?=$arActivity['time']['from']?> : <?=$arActivity['time']['to']?></span>
											<?php endif; ?>
											<?php if(isset($arActivity['theme'])): ?>
												<b><?=$arActivity['name']?></b>
												<p><?=$arActivity['theme']?></p>
											<?php else: ?>
												<p><?=$arActivity['name']?></p>
											<?php endif; ?>
										</li>
									<?php endforeach; ?>
								</ul>
						</div>
					</div>
					<div class="border"></div>
				</div>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
</div>