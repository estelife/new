<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="el-ajax-detail">
	<div class="block" rel="clinic">
		<div class='block-header red'>
			<span><?=$arResult['company']['name']?></span>
		</div>
		<div class='shadow'></div>
		<div class="el-ditem el-ditem-h">
			<div class="logo el-col">
				<?if (!empty($arResult['company']['img'])):?>
					<?=$arResult['company']['img']?>
				<?php else:?>
					<img src ="/bitrix/templates/web20/images/unlogo.png" alt="<?=$arResult['company']['name']?>" />
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
									<?php if (!empty($arResult['company']['web'])):?>
										<li><span>Сайт</span><span><a href="<?=$arResult['company']['web']?>" target="_blank"><?=$arResult['company']['web_short']?></a></span></li>
									<?php endif?>
								</ul>
							</td>
						</tr></table>
				</div>
			</div>
			<h3>О компании</h3>
			<p><?=$arResult['company']['detail_text']?></p>
		</div>
	</div>

<?php if (!empty($arResult['production'])):?>
	<div class="block" rel="clinic">
		<div class='block-header blue'>
			<span>Продукция</span>
		</div>
		<div class="el-ditem-action production-events production" data-scroll="true">
			<?php foreach ($arResult['production'] as $arValue):?>

				<div class="section big">
					<a href="<?=$arValue['link']?>">
						<div class="h"><?=$arValue["name"]?></div>
					</a>
					<div class="i">
						<?php if(!empty($arValue["logo_id"])):?>
							<?=$arValue["img"]?>
						<?endif?>
					</div>

					<div class="t"><?=$arValue["preview_text"]?></div>

				</div>
			<?php endforeach?>
			<div class="clear"></div>
		</div>
	</div>
<?php endif?>
<?php if (!empty($arResult['training'])):?>
	<div class="block" rel="clinic">
		<div class='block-header blue'>
			<span>Обучение</span>
		</div>
		<div class="el-ditem-action training-events production" >
			<?php foreach ($arResult['training'] as $arValue):?>

				<div class="section big">
					<a href="<?=$arValue['link']?>">
						<div class="h"><?=$arValue["event_name"]?></div>
					</a>
					<div class="if">
						<?php if(!empty($arValue["company_logo"])):?>
							<?=$arValue["img"]?>
						<?php else:?>
							<img src ="/bitrix/templates/web20/images/unlogo.png" alt="<?=$arValue['event_name']?>" />
						<?php endif?>
					</div>
					<div class="d">
						<table class="data">
							<tbody>
							<?php if (!empty($arValue['calendar'])):?>
								<?php foreach ($arValue['calendar'] as $key=>$val):?>
									<?php if ($key<4):?>
										<tr>
											<td><i class="icon calendar"></i></td>
											<td><?=$val['full_date']?></td>
										</tr>
									<?php endif?>
								<?php endforeach?>
							<?php endif?>
							</tbody>
						</table>
					</div>
					<div class="clear"></div>

					<div class="t">
						<table class="data">
							<?php if (!empty($arValue['address'])):?>
								<tr>
									<td><i class="icon address"></i></td>
									<td><div>г. <?=$arValue['city']?> <?=$arValue['address']?></div></td>
								</tr>
							<?php endif?>
							<?php if (!empty($arValue['company_name'])):?>
								<tr>
									<td><i class="icon company"></i></td>
									<td><div><?=$arValue['company_name']?></div></td>
								</tr>
							<?php endif?>
							<?php if (!empty($arValue['web'])):?>
								<tr>
									<td><i class="icon link"></i></td>
									<td><div><a target="_blank" href="<?=$arValue['web']?>"><?=$arValue['web_short']?></a></div></td>
								</tr>
							<?php endif?>
							<?php if (!empty($arValue['phone'])):?>
								<tr>
									<td><i class="icon phone"></i></td>
									<td><div><b><?=$arValue['phone']?></b></div></td>
								</tr>
							<?php endif?>
						</table>
					</div>

				</div>
			<?php endforeach?>
			<div class="clear"></div>
		</div>
	</div>
<?php endif?>
</div>

<div class="block none" rel="news">
	<div class="block-header red">
		<h1><?=GetMessage("ESTELIFE_APPARATUS")?></h1>
		<div class="clear"></div>
	</div>
	<div class="shadow"></div>
	<div class="tab-group" rel="articles">
		<div class="el-items"></div>
		<div class="el-not-found none"><?=GetMessage("ESTELIFE_APPARATUS_NOT_FOUND")?></div>
		<div class="clear"></div>
		<div class="pagination"></div>
	</div>
</div>