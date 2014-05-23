<?php
$arPatches = isset($arResult['DEEP_PATHES']) ? $arResult['DEEP_PATHES'] : array();
$nCount = count($arPatches);

if (!$nCount || $nCount > 1)
	throw new \core\exceptions\VHttpEx('Not Found', 404);

$arResult['CURRENT_TAB'] = $arPatches[0];
require_once __DIR__.'/clinics.php';