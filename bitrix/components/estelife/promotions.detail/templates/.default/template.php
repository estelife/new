<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="el-ajax-detail">
	<div class="block el-block" rel="clinic">
		<div class='block-header red'>
			<span><?=$arResult['action']['preview_text']?></span>
			<div class="clear"></div>
			<div class='shadow'></div>
		</div>
		<div class="el-ditem el-ditem-h photo_slider">
			<?if (!empty($arResult['action']['photos'])):?>
				<div class="big-photo">
					<?php foreach ($arResult['action']['photos'] as $val):?>
							<?=$val?>
					<?php endforeach?>
					<?php if ($arResult['action']['photos_count']>1):?>
						<span class="arrow left el-cm"></span>
						<span class="arrow right el-cm"></span>
					<?php endif?>
				</div>
			<?endif?>
			<br />
			<div>
				<?=$arResult['action']['detail_text']?>
			</div>
			<?php if (!empty($arResult['action']['prices'])):?>
				<h3>Примеры цен</h3>
				<div class="el-prop">
					<ul>
						<?php foreach ($arResult['action']['prices'] as $val):?>
							<li><?=$val['procedure']?> <s>от <?=$val['old_price']?>р.</s> от <?=$val['new_price']?>р.</li>
						<?php endforeach?>
					</ul>
				</div>
			<?php endif?>
			<?php if (!empty($arResult['action']['clinics'])):?>
				<?php foreach ($arResult['action']['clinics'] as $val):?>
					<h3><a href="<?=$val['link']?>" target="_blank"><?=$val['clinic_name']?></a></h3>
					<div class="el-all-contacts">
						<?php if (!empty($val['clinic_address'])):?>
							<div class="el-col el-prop">
								<h4>Адрес</h4>
								<ul>
									<li>г. <?=$val['city']?> <?=$val['clinic_address']?></li>
								</ul>
							</div>
						<?php endif?>
						<?php if (!empty($val['phone'])):?>
							<div class="el-col el-col-r el-prop">
								<h4>Телефон</h4>
								<ul>
									<li><?=$val['phone']?></li>
								</ul>
							</div>
						<?php endif?>
						<div class="clear"></div>
					</div>
				<?php endforeach?>
			<?php endif?>
		</div>
		<div class='block-header blue'>
			<span class="el-center">Срок действия акции по <?=$arResult['action']['end_date']?></span>
			<div class="clear"></div>
			<div class='shadow'></div>
		</div>
	</div>
</div>
<div class="block none" rel="news">
	<div class="block-header blue">
		<h1><?=GetMessage("ESTELIFE_ACTIONS")?></h1>
		<div class="clear"></div>
	</div>
	<div class="shadow"></div>
	<div class="tab-group" rel="articles">
		<div class="panel">
			<div class="el-items"></div>
			<div class="el-not-found none"><?=GetMessage("ESTELIFE_ACTION_NOT_FOUND")?></div>
			<div class="clear"></div>
			<div class="pagination"></div>
		</div>
	</div>
</div>