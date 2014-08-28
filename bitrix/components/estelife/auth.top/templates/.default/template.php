<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<!--
<?php if ($arResult['auth']):?>
	<a href="/personal/auth/?backurl=<?=$arResult['backurl']?>&logout=yes" class="cols goto-auth logout">Выйти</a>
<?php else:?>
	<a href="/personal/auth/?backurl=<?=$arResult['backurl']?>" class="cols goto-auth">Войти</a>
<?php endif?>
-->