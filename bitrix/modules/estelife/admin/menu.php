<?php
IncludeModuleLangFile(__FILE__);

if($APPLICATION->GetGroupRight("estelife")>"D")
{
	AddEventHandler("main", "OnBuildGlobalMenu", "InitGlobalMenu");
	function InitGlobalMenu(&$aGlobalMenu, &$aModuleMenu){
		global $USER;

		$aGlobalMenu['global_menu_estelife']=array(
			"menu_id" => "estelife",
			//"icon" => "button_settings",
			"page_icon" => "estelife_title_icon",
			"index_icon" => "estelife_page_icon",
			"text" => GetMessage("ESTELIFE_MENU_MAIN"),
			"title" => GetMessage("ESTELIFE_MENU_MAIN_TITLE"),
			"sort" => 600,
			"items_id" => "global_menu_estelife",
			"help_section" => "estelife",
			"items" => array(
				array(
					"text" => GetMessage("ESTELIFE_SERVICE_REFERENCE"),
					"dynamic" => true,
					"module_id" => "estelife",
					"title" => GetMessage("ESTELIFE_SERVICE_REFERENCE_TITLE"),
					"items_id" => "menu_estelife_servive_reference",
					"items" => array(
						array(
							"text" => GetMessage("ESTELIFE_SPECIALIZATION"),
							"dynamic" => true,
							"module_id" => "estelife",
							"title" => GetMessage("ESTELIFE_SPECIALIZATION_TITLE"),
							"items_id" => "menu_estelife_specialization",
							"url" => '/bitrix/admin/estelife_specialization_list.php?lang='.LANGUAGE_ID,
							'more_url'=>array(
								'/bitrix/admin/estelife_specialization_edit.php?lang='.LANGUAGE_ID,
							)
						),
						array(
							"text" => GetMessage("ESTELIFE_SERVICE"),
							"dynamic" => true,
							"module_id" => "estelife",
							"title" => GetMessage("ESTELIFE_SERVICE_TITLE"),
							"items_id" => "menu_estelife_service",
							"url" => '/bitrix/admin/estelife_service_list.php?lang='.LANGUAGE_ID,
							'more_url'=>array(
								'/bitrix/admin/estelife_service_edit.php?lang='.LANGUAGE_ID,
							)
						),
						array(
							"text" => GetMessage("ESTELIFE_METHODS"),
							"dynamic" => true,
							"module_id" => "estelife",
							"title" => GetMessage("ESTELIFE_METHODS_TITLE"),
							"items_id" => "menu_estelife_methods",
							"url" => '/bitrix/admin/estelife_method_list.php?lang='.LANGUAGE_ID,
							'more_url'=>array(
								'/bitrix/admin/estelife_method_edit.php?lang='.LANGUAGE_ID,
							)
						),
						array(
							"text" => GetMessage("ESTELIFE_SERVICE_CONCREATE"),
							"dynamic" => true,
							"module_id" => "estelife",
							"title" => GetMessage("ESTELIFE_SERVICE_CONCREATE_TITLE"),
							"items_id" => "menu_estelife_servive_concreate",
							"url" => '/bitrix/admin/estelife_service_concreate_list.php?lang='.LANGUAGE_ID,
							'more_url'=>array(
								'/bitrix/admin/estelife_service_concreate_edit.php?lang='.LANGUAGE_ID,
							)
						)
					)
				),
				array(
					"text" => GetMessage("ESTELIFE_CLINICS"),
					"dynamic" => true,
					"module_id" => "estelife",
					"title" => GetMessage("ESTELIFE_CLINICS_TITLE"),
					"items_id" => "menu_estelife_clinics",
					"items" => array(
						array(
							"text" => GetMessage("ESTELIFE_CLINIC_LIST"),
							"dynamic" => true,
							"module_id" => "estelife",
							"title" => GetMessage("ESTELIFE_CLINIC_LIST_TITLE"),
							"items_id" => "menu_estelife_clinic_list",
							"url" => '/bitrix/admin/estelife_clinic_list.php?lang='.LANGUAGE_ID,
							'more_url'=>array(
								'/bitrix/admin/estelife_clinic_edit.php?lang='.LANGUAGE_ID,
							)
						),
						array(
							"text" => GetMessage("ESTELIFE_AKZII"),
							"dynamic" => true,
							"module_id" => "estelife",
							"title" => GetMessage("ESTELIFE_AKZII_TITLE"),
							"items_id" => "menu_estelife_akzii_list",
							"url" => '/bitrix/admin/estelife_akzii_list.php?lang='.LANGUAGE_ID,
							'more_url'=>array(
								'/bitrix/admin/estelife_akzii_edit.php?lang='.LANGUAGE_ID,
							)
						)
					)
				),

				array(
					"text" => GetMessage("ESTELIFE_EVENTS"),
					"dynamic" => true,
					"module_id" => "estelife",
					"title" => GetMessage("ESTELIFE_EVENTS_TITLE"),
					"items_id" => "menu_estelife_events",
					"items" => array(
						array(
							"text" => GetMessage("ESTELIFE_TRAINING"),
							"dynamic" => true,
							"module_id" => "estelife",
							"title" => GetMessage("ESTELIFE_TRAINING_TITLE"),
							"items_id" => "menu_estelife_training",
							"url" => '/bitrix/admin/estelife_training_list.php?lang='.LANGUAGE_ID,
							'more_url'=>array(
								'/bitrix/admin/estelife_training_edit.php?lang='.LANGUAGE_ID,
							)
						),
						array(
							"text" => GetMessage("ESTELIFE_ACTIVITY"),
							"dynamic" => true,
							"module_id" => "estelife",
							"title" => GetMessage("ESTELIFE_ACTIVITY_TITLE"),
							"items_id" => "menu_estelife_activity",
							"url" => '/bitrix/admin/estelife_activity_list.php?lang='.LANGUAGE_ID,
							'more_url'=>array(
								'/bitrix/admin/estelife_activity_edit.php?lang='.LANGUAGE_ID,
							)
						)
					)
				),

				array(
				    "text" => GetMessage("ESTELIFE_COMPANIES"),
				    "dynamic" => true,
				    "module_id" => "estelife",
				    "title" => GetMessage("ESTELIFE_COMPANIES_TITLE"),
				    "items_id" => "menu_estelife_companies",
				    "items" => array(
						array(
							"text" => GetMessage("ESTELIFE_COMPANIES_TITLE"),
							"dynamic" => true,
							"module_id" => "estelife",
							"title" => GetMessage("ESTELIFE_COMPANIES_TITLE"),
							"items_id" => "menu_estelife_companies2",
							"url" => '/bitrix/admin/estelife_company_list.php?lang='.LANGUAGE_ID,
							'more_url'=>array(
								'/bitrix/admin/estelife_company_edit.php?lang='.LANGUAGE_ID,
							)
						)
				    )
				),
				array(
					"text" => GetMessage("ESTELIFE_PRODUCTION"),
					"dynamic" => true,
					"module_id" => "estelife",
					"title" => GetMessage("ESTELIFE_PRODUCTION_TITLE"),
					"items_id" => "menu_estelife_production",
					"items" => array(
						array(
							"text" => GetMessage("ESTELIFE_PILLS"),
							"dynamic" => true,
							"module_id" => "estelife",
							"title" => GetMessage("ESTELIFE_PILLS_TITLE"),
							"items_id" => "menu_estelife_pills",
							"url" => '/bitrix/admin/estelife_pills_list.php?lang='.LANGUAGE_ID,
							'more_url'=>array(
								'/bitrix/admin/estelife_pills_edit.php?lang='.LANGUAGE_ID,
							)
						),
						array(
							"text" => GetMessage("ESTELIFE_APPARATUS"),
							"dynamic" => true,
							"module_id" => "estelife",
							"title" => GetMessage("ESTELIFE_APPARATUS_TITLE"),
							"items_id" => "menu_estelife_apparatus",
							"url" => '/bitrix/admin/estelife_apparatus_list.php?lang='.LANGUAGE_ID,
							'more_url'=>array(
								'/bitrix/admin/estelife_apparatus_edit.php?lang='.LANGUAGE_ID,
							)
						)
					)
				),
			)
		);

		return $aGlobalMenu;
	}

	return $aMenu;
}
return false;