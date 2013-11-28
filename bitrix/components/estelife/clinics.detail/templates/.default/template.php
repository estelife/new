<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="el-ajax-detail">
<div class="block" rel="clinic">
	<div class='block-header red'>
		<h1><?=$arResult['clinic']['name']?></h1>
	</div>
	<div class='shadow'></div>
	<div class="el-ditem el-ditem-h">
		<div class="logo el-col">
			<?=$arResult['clinic']['logo']?>
		</div>
		<div class="el-scroll next_prev_contact">
			<div class="slider_content">
				<?php foreach ($arResult['clinic']['contacts'] as $key=>$val):?>
					<div class="el-scroll-in ">
						<table><tr>
								<td>
									<ul class="contacts el-col el-ul el-contacts">
										<?php if (!empty($val['city'])):?>
										<li><span>Город</span><span><?=$val['city']?></span></li>
										<?php endif?>
										<?php if (!empty($val['address'])):?>
										<li><span>Адрес</span><span><?=$val['address']?></span></li>
										<?php endif?>
										<?php if (!empty($val['metro'])):?>
										<li><span>Метро</span><span><?=$val['metro']?></span></li>
										<?php endif?>
										<?php if (!empty($val['phone'])):?>
										<li><span>Телефон</span><span><?=$val['phone']?></span></li>
										<?php endif?>
										<?php if (!empty($val['web'])):?>
										<li><span>Сайт</span><span><a href="<?=$val['web']?>" target="_blank"><?=$val['web_short']?></a></span></li>
										<?php endif?>
									</ul>
								</td>
							</tr></table>
					</div>
				<?php endforeach?>
				</div>
			<?php if ($arResult['clinic']['count']>1):?>
				<span class="left"></span>
				<span class="right"></span>
			<?php endif?>
		</div>


		<div class="clear"></div>
		<?php if (!empty($arResult['clinic']['detail_text'])):?>
		<h3>О клинике</h3>
		<p><?=$arResult['clinic']['detail_text']?></p>
		<?php endif?>
		<?php if (!empty($arResult['clinic']['pays'])):?>
		<div class="el-prop">
			<h3>Способы оплаты</h3>
				<ul class="el-ul">
					<?php foreach ($arResult['clinic']['pays'] as $val):?>
						<li><?=$val['name']?></li>
					<?php endforeach?>
				</ul>
		</div>
		<?php endif?>
	</div>
</div>
<?php if (!empty($arResult['clinic']['specialization'])):?>
	<div class="block" rel="clinic">
		<div class='block-header blue'>
			<span>Услуги</span>
		</div>
		<?php foreach ($arResult['clinic']['specialization'] as $key=>$val):?>
			<div class="el-ditem">
				<div class="title"><?=$val['s_name']?></div>
					<?$i=0?>
					<?php foreach ($arResult['clinic']['service'] as $k=>$v):?>
						<?php if ($i%2 == 0):?><div class="el-row"><?endif?>
						<?php if ($key == $v['s_id']):?>
							<div class="el-prop el-col">
								<h3><?=$v['ser_name']?></h3>
								<ul class="el-ul">
									<?php foreach ($arResult['clinic']['con'] as $kk=>$vv):?>
										<?php if ($k == $vv['ser_id']):?>
											<li><?=$vv['con_name']?></li>
										<?php endif?>
									<?php endforeach?>
								</ul>
							</div>
						<?php endif?>
						<?php if ($i%2 == 0):?></div><?endif?>
						<?$i++?>
					<?php endforeach?>
				<div class="clear"></div>
			</div>
		<?php endforeach?>
	</div>
<?php endif?>
<?php if (!empty($arResult['clinic']['akzii'])):?>
<div class="block" rel="clinic">
	<div class='block-header blue'>
		<span>Акции</span>
	</div>
	<div class="el-ditem-action" id="actions" >
		<?php foreach ($arResult['clinic']['akzii'] as $arValue):?>

		<div class="articlee">
			<div class="section big">
				<a href="<?=$arValue["link"]?>">
					<div class="h"><?=$arValue["name"]?></div>
					<?php if(!empty($arValue["logo"])):?>
						<img class="photo" src="<?=$arValue['logo']?>" alt="<?=$arValue["name"]?>" title="<?=$arValue["name"]?>" />
					<?endif?>
					<span class="new_price"><?=intval($arValue['new_price'])?> руб.</span>
					<span class="old_price"><span></span><?=intval($arValue['old_price'])?> руб.</span>
					<span class="days"><span></span>Осталось <?=$arValue['time']?> <?=$arValue['day']?></span>
					<span class="discount"><?=$arValue["sale"]?> %</span>
				</a>
			</div>
		</div>
		<?php endforeach?>
		<div class="clear"></div>
	</div>
</div>
<?php endif?>
<?php if (!empty($arResult['clinic']['gallery'])):?>
	<div class="block" rel="clinic">
		<div class='block-header blue'>
			<span>Фотографии</span>
		</div>
		<div class="dl_item">
			<div class="el-gallery">
				<?php foreach ($arResult['clinic']['gallery'] as $val):?>
					<div class="image">
						<a href="<?=$val['original']?>" class="colorbox" rel="clinic" target="_blank" title="<?=$val['description']?>">
							<img src="<?=$val['original']?>" alt="<?=$val['description']?>" title="<?=$val['description']?>" />
							<span class="desc"><?=$val['description']?></span>
						</a>
					</div>
				<?php endforeach?>
			</div>
		</div>
	</div>
<?php endif?>
</div>

<div class="block none" rel="news">
	<div class="block-header red">
		<span><?=GetMessage("ESTELIFE_CLINIC")?></span>
		<div class="clear"></div>
	</div>
	<div class="shadow"></div>
	<div class="tab-group" rel="articles">
		<div class="el-items"></div>
		<div class="el-not-found none"><?=GetMessage('ESTELIFE_CLINIC_NOT_FOUND')?></div>
		<div class="clear"></div>
		<div class="pagination"></div>
	</div>
</div>