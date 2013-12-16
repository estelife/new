<?
$arUrlRewrite = array(
	array(
		"CONDITION" => "#^/[a-z]{2}[0-9]+/#",
		"RULE" => "",
		"ID" => "estelife:mega.complex.detail",
		"PATH" => "/system/detail.php",
	),
	array(
		"CONDITION" => "#^/([a-zA-Z_-]+)/([a-zA-Z0-9_-]+/)?#",
		"RULE" => "",
		"ID" => "estelife:mega.complex.section",
		"PATH" => "/system/list.php",
	),
);