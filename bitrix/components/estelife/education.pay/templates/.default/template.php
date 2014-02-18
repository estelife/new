<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
?>
<form action="https://www.onlinedengi.ru/wmpaycheck.php" method="post" name="add_request">
	<input type="hidden" name="project" value="<?=$arResult['project_id']?>" />
	<input type="hidden" name="source" value="<?=$arResult['source_id']?>" />
	<input type="hidden" name="mode_type" value="468" />
	<input type="hidden" name="amount" value="1" />
	<input type="hidden" name="nickname" value="<?=$arResult['receipt_id']?>" />

	<input type="submit" class="submit" value="Оплатить" />
</form>