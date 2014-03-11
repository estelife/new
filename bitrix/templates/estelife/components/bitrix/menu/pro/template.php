<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>
	<ul>
		<?foreach($arResult as $arItem):?>
			<li>
				<a href="<?=$arItem["LINK"]?>">
					<span><?=$arItem["TEXT"]?></span>
					<?=$arItem["PARAMS"]["INFO"]?>
				</a>
			</li>
		<?endforeach?>
	</ul>
<?endif?>