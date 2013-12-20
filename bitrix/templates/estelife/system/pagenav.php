<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
?>

<ul class="nav">
	<?php
	if(!empty($nEndPage)):
		if($nStartPage>1):?>
			<li><a href="<?=$sUrlPath?>?PAGEN_<?=$this->NavNum?>=1<?=$strNavQueryString?>">1</a></li>
			<li><span>...</span></li>
		<?php
		endif;

		$NavRecordGroup = $nStartPage;
		while($NavRecordGroup <= $nEndPage)
		{
			if($NavRecordGroup == $this->NavPageNomer): ?>
				<li><b><?=$NavRecordGroup?></b></li>
			<?php else: ?>
				<li><a href="<?=$sUrlPath?>?PAGEN_<?=$this->NavNum?>=<?=$NavRecordGroup.$strNavQueryString?>" data-page="<?=$NavRecordGroup?>"><?=$NavRecordGroup?></a></li>
			<?php endif;
			$NavRecordGroup++;
		}

		if($nEndPage<$this->NavPageCount):?>
			<li><span>...</span></li>
			<li><a href="<?=$sUrlPath?>?PAGEN_<?=$this->NavNum?>=<?=$this->NavPageCount.$strNavQueryString?>"><?=$this->NavPageCount?></a></li>
		<?php
		endif;
	else:?>
			<li><b>1</b></li>
	<?php endif; ?>
</ul>