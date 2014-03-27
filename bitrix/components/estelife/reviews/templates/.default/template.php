<?php
if ($arResult['show_form'])
	$APPLICATION->IncludeComponent("estelife:review.form", "", array(
		'clinic_id' => $arResult['clinic_id']
	));
else
	$APPLICATION->IncludeComponent("estelife:review.list", "", array(
		'clinic_id' => $arResult['clinic_id']
	));