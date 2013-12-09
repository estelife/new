<ul class="nav">
	<?php
	if(!empty($nEndPage)):
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
	else:?>
			<li><b>1</b></li>
	<?php endif; ?>
</ul>