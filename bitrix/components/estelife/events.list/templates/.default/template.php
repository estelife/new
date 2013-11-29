<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<div class="block" rel="news">
	<div class="block-header blue">
		<h1><?=GetMessage("ESTELIFE_EVENTS")?></h1>
		<div class="clear"></div>
	</div>
	<div class="shadow"></div>
	<div class="tab-group" rel="articles">
		<div class="el-items">
		<?php if (!empty($arResult['events'])):?>
			<?php foreach ($arResult['events'] as $arEvent):?>
				<div class="news-item clinic-list el-item"><div class='item-wrap'>
						<h2 class="clinic-title"><a href="<?=$arEvent['link']?>" class="el-get-detail"><?=$arEvent["name"]?></a></h2>
						<div class="full_name">
							<?=$arEvent["full_name"]?>
						</div>
						<div class='news-picture'>
							<div>
								<a href="<?=$arEvent['link']?>" class="el-get-detail">
								<?php if(!empty($arEvent["logo"])):?>
									<?=$arEvent['logo']?>
								<?endif?>
								</a>
							</div>

						</div>
						<table class='clinic-table float'>
							<tr>
								<td valign="top">
									<table class='data'>
										<? if(!empty($arEvent["country_name"])):?>
											<tr>
												<td><i class='icon address'></i></td>
												<td><?=$arEvent["country_name"]?>, г. <?=$arEvent["city_name"]?></td>
											</tr>
										<?php endif?>
										<? if(!empty($arEvent["web"])):?>
											<tr>
												<td><i class='icon link'></i></td>
												<td><a target='_blank' href="<?=$arEvent["web"]?>"><?=$arEvent["web_short"]?></a></td>
											</tr>
										<?php endif?>
									</table>
								</td>
								<td valign="top" class="profs">
									<?php if (!empty($arEvent['calendar'])):?>
										<table class="data">
											<?php foreach ($arEvent['calendar'] as $val):?>
												<tr>
													<td><i class="icon calendar"></i></td>
													<td>
														<?=$val['from']?>
														<?php if(!empty($val['to'])):?>
															-
															<?=$val['to']?>
														<?php endif; ?>
													</td>
												</tr>
											<?php endforeach?>
										</table>
									<?php endif?>
								</td>
							</tr>
							<tr>
								<td colspan="2" class="preview_table">
									<?=$arEvent["preview_text"]?>
								</td>
							</tr>
						</table>
						<div class='clear'></div>
					</div>
				</div>
			<?php endforeach?>
		<?php endif?>
		</div>

		<div class="el-not-found<?=(!empty($arResult['events']) ? ' none' : '')?>"><?=GetMessage("ESTELIFE_EVENTS_NOT_FOUND")?></div>

		<div class='clear'></div>
		<?php if (!empty($arResult['nav'])):?>
			<div class='pagination'>
				<?=$arResult['nav']?>
			</div>
		<?php endif?>
	</div>
</div>