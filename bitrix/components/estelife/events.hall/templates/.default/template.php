<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<ul class="crumb">
	<li><a href="/">Главная</a></li>
	<li><a href="/events/">Календарь событий</a></li>
	<li><a href="/ev<?=$arResult['event_id']?>/"><?=$arResult['event']?></a></li>
	<li><a href="/ev<?=$arResult['event_id']?>/program/">Программа мероприятия</a></li>
	<li><b><?=$arResult['hall']?></b></li>
</ul>
<div class="hall">
	<h1><?=$arResult['event']?></h1>
	<h2>Программа мероприятия</h2>
	<div class="title">
		<div class="date"><?=$arResult['date']?>. <?=$arResult['hall']?><i></i></div>
		<a href="/ev<?=$arResult['event_id']?>/program/" class="ax-support">Полная программа конгресса</a>
	</div>
	<?php if (!empty($arResult['sections'])):?>
		<?php foreach ($arResult['sections'] as $key=>$val):?>
			<div class="items">
				<?php if ($key>0):?>
					<div class="h<?=(empty($val['activities']) ? ' no-bo' : '')?>">
						<b><?=$val['section_name']?></b>
						<?php if(isset($val['time'])): ?>
							<span><?=$val['time']['from']?> - <?=$val['time']['to']?></span>
						<?php endif; ?>
						<h3><?=$val['section_theme']?></h3>
					</div>
				<?php endif?>

				<?php if (!empty($val['activities'])):?>
					<?php foreach($val['activities'] as $arActivity): ?>
						<div class="item activity">
							<h4><?=$arActivity['activity_name']?></h4>

							<?php if (!empty($arActivity['events'])):?>
								<?php foreach ($arActivity['events'] as $v):?>
									<div class="user">
										<div class="img">
											<div class="img-in">
											<?php if(!empty($v['logo'])): ?>
												<?=$v['logo']?>
											<?php else: ?>
												<div class="default">Изображение отсутствует</div>
											<?endif?>
											</div>
										</div>
										<div class="about">
											<div class="about-in">
												<a href="<?=$v['link']?>"><?=$v['name']?></a>
												<?php if($v['country_id']):?>
													<span class="country c<?=$v['country_id']?>"><?=$v['country_name']?></span>
												<?php endif; ?>
												<?php if($v['description']): ?>
													<p><?=$v['description']?></p>
												<?php endif; ?>
											</div>
										</div>
									</div>
								<?php endforeach?>
							<?php endif?>
						</div>
					<?php endforeach; ?>
				<?php endif?>
			</div>
		<?php endforeach?>
	<?php endif?>
</div>