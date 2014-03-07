<?
$arUrlRewrite = array(
	array(
		"CONDITION" => "#^/yvoire/#",
		"RULE" => "",
		"ID" => "estelife:yvoire",
		"PATH" => "/yvoire/index.php",
	),
	array(
		"CONDITION" => "#^/personal/([a-zA-Z0-9_-]+/)?#",
		"RULE" => "",
		"ID" => "estelife:mega.complex.personal",
		"PATH" => "/personal/index.php",
	),
	array(
		"CONDITION" => "#^/rest/[a-z]{2}[0-9]+/#",
		"RULE" => "",
		"ID" => "estelife:mega.complex.detail",
		"PATH" => "/rest/detail.php",
	),
	array(
		"CONDITION" => "#^/rest/([a-zA-Z_-]+)/([a-zA-Z0-9_-]+/)?#",
		"RULE" => "",
		"ID" => "estelife:mega.complex.section",
		"PATH" => "/rest/list.php",
	),
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
	array(
		"CONDITION" => "#^/[0-9A-Za-z-_]+/([a-zA-Z0-9_-]/)?#",
		"RULE" => "",
		"ID" => "estelife:mega.complex.section",
		"PATH" => "/system/list.php",
	),
);