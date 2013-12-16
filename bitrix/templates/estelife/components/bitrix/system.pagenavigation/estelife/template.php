<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<ul class="nav">
	<?php

	if(!empty($arResult['nEndPage']) && $arResult['nEndPage']>1):
		$NavRecordGroup = $arResult['nStartPage'];
		while($NavRecordGroup <= $arResult['nEndPage'])
		{
			if($NavRecordGroup == $arResult['NavPageNomer']): ?>
				<li><b><?=$NavRecordGroup?></b></li>
			<?php else: ?>
				<li><a href="<?=$arResult['sUrlPath']?>?PAGEN_<?=$arResult['NavNum']?>=<?=$NavRecordGroup?>&<?=$arResult['NavQueryString']?>" data-page="<?=$NavRecordGroup?>"><?=$NavRecordGroup?></a></li>
			<?php endif;
			$NavRecordGroup++;
		}
	else:?>

	<?php endif; ?>
</ul>