<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php if (!empty($arResult)):?>
	<div class="media">
		<div class="content">
			<div class="title">
				<h2>Медиа</h2>
				<!--<a href="/photo/">Смотреть больше</a>-->
			</div>
			<ul class="menu">
				<li class="first"><a href="#" class="get_photos_and_videos" rel="ALL">x</a></li>
				<li><a href="#" class="get_only_photos" rel="ONLY_PHOTO">Только фото</a></li>
				<li><a href="#" class="get_only_videos" rel="ONLY_VIDEO">Только видео</a></li>
			</ul>
			<div class="items">
				<?php foreach ($arResult as $key=>$val):?>
					<div class="item <?php if ($val['IS_VIDEO']== 'Y'):?>video<?php endif?> <?php if (($key+1)%6 ==0):?>last<?php endif?>" data-id="<?=$val['ID']?>">
						<?php if ($val['IS_VIDEO']== 'Y'):?><span></span><?php endif?>
						<img src="<?=$val['IMG']?>" alt="<?=$val['NAME']?>" title="<?=$val['NAME']?>" width="146px" height="100px" />
						<div class="border"></div>
					</div>
				<?php endforeach?>
			</div>
		</div>
	</div>
<?php endif?>