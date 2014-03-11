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
					"text" => GetMessage("ESTELIFE_TRAINING"),
					"dynamic" => true,
					"module_id" => "estelife",
					"title" => GetMessage("ESTELIFE_TRAINING_TITLE"),
					"items_id" => "menu_estelife_training",
					"url" => '/bitrix/admin/estelife_training_list.php?lang='.LANGUAGE_ID,
					'more_url'=>array(
						'/bitrix/admin/estelife_training_edit.php?lang='.LANGUAGE_ID,
					),
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
					),
					"items" => array(
						array(
							"text" => GetMessage("ESTELIFE_ACTIVITY_TYPES"),
							"dynamic" => true,
							"module_id" => "estelife",
							"title" => GetMessage("ESTELIFE_ACTIVITY_TYPES_TITLE"),
							"items_id" => "menu_estelife_activity_types",
							"url" => '/bitrix/admin/estelife_activity_types_list.php?lang='.LANGUAGE_ID,
							'more_url'=>array(
								'/bitrix/admin/estelife_activity_types_edit.php?lang='.LANGUAGE_ID,
							)
						),
						array(
							"text" => GetMessage("ESTELIFE_EVENT_HALLS"),
							"dynamic" => true,
							"module_id" => "estelife",
							"title" => GetMessage("ESTELIFE_EVENT_HALLS_TITLE"),
							"items_id" => "menu_estelife_event_halls",
							"url" => '/bitrix/admin/estelife_event_halls_list.php?lang='.LANGUAGE_ID,
							'more_url'=>array(
								'/bitrix/admin/estelife_event_halls_edit.php?lang='.LANGUAGE_ID,
							)
						),
						array(
							"text" => GetMessage("ESTELIFE_EVENT_SECTIONS"),
							"dynamic" => true,
							"module_id" => "estelife",
							"title" => GetMessage("ESTELIFE_EVENT_SECTIONS_TITLE"),
							"items_id" => "menu_estelife_event_sections",
							"url" => '/bitrix/admin/estelife_event_sections_list.php?lang='.LANGUAGE_ID,
							'more_url'=>array(
								'/bitrix/admin/estelife_event_sections_edit.php?lang='.LANGUAGE_ID,
							)
						),
						array(
							"text" => GetMessage("ESTELIFE_PROFESSIONALS_CLINICS"),
							"dynamic" => true,
							"module_id" => "estelife",
							"title" => GetMessage("ESTELIFE_PROFESSIONALS_CLINICS_TITLE"),
							"items_id" => "menu_estelife_professionals_clinics",
							"url" => '/bitrix/admin/estelife_professionals_list.php?lang='.LANGUAGE_ID,
							'more_url'=>array(
								'/bitrix/admin/estelife_professionals_edit.php?lang='.LANGUAGE_ID,
							)
						),
						array(
							"text" => GetMessage("ESTELIFE_EVENT_ACTIVITIES"),
							"dynamic" => true,
							"module_id" => "estelife",
							"title" => GetMessage("ESTELIFE_EVENT_ACTVITIES_TITLE"),
							"items_id" => "menu_estelife_event_activities",
							"url" => '/bitrix/admin/estelife_event_activities_list.php?lang='.LANGUAGE_ID,
							'more_url'=>array(
								'/bitrix/admin/estelife_event_activities_edit.php?lang='.LANGUAGE_ID,
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
							"text" => GetMessage("ESTELIFE_THREADS"),
							"dynamic" => true,
							"module_id" => "estelife",
							"title" => GetMessage("ESTELIFE_THREADS_TITLE"),
							"items_id" => "menu_estelife_threads",
							"url" => '/bitrix/admin/estelife_threads_list.php?lang='.LANGUAGE_ID,
							'more_url'=>array(
								'/bitrix/admin/estelife_threads_edit.php?lang='.LANGUAGE_ID,
							)
						),
						array(
							"text" => GetMessage("ESTELIFE_IMPLANTS"),
							"dynamic" => true,
							"module_id" => "estelife",
							"title" => GetMessage("ESTELIFE_IMPLANTS_TITLE"),
							"items_id" => "menu_estelife_implants",
							"url" => '/bitrix/admin/estelife_implants_list.php?lang='.LANGUAGE_ID,
							'more_url'=>array(
								'/bitrix/admin/estelife_implants_edit.php?lang='.LANGUAGE_ID,
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
				array(
					"text" => GetMessage("ESTELIFE_SPECIAL"),
					"dynamic" => true,
					"module_id" => "estelife",
					"title" => GetMessage("ESTELIFE_SPECIAL"),
					"items_id" => "menu_estelife_special",
					"items" => array(
						array(
							"text" => GetMessage("ESTELIFE_SUBSCRIBE_LIST"),
							"dynamic" => true,
							"module_id" => "estelife",
							"title" => GetMessage("ESTELIFE_SUBSCRIBE_LIST"),
							"items_id" => "menu_estelife_subscribe",
							"url" => '/bitrix/admin/estelife_subscribe_list.php?lang='.LANGUAGE_ID,
							'more_url'=>array(
								'/bitrix/admin/estelife_subscribe_edit.php?lang='.LANGUAGE_ID,
							)
						),
						array(
							"text" => GetMessage("ESTELIFE_REQUEST_LIST"),
							"dynamic" => true,
							"module_id" => "estelife",
							"title" => GetMessage("ESTELIFE_REQUEST_LIST"),
							"items_id" => "menu_estelife_request",
							"url" => '/bitrix/admin/estelife_request_list.php?lang='.LANGUAGE_ID,
							'more_url'=>array(
								'/bitrix/admin/estelife_request_edit.php?lang='.LANGUAGE_ID,
							)
						),
					)
				),
				array(
					"text" => GetMessage("ESTELIFE_COMMENTS"),
					"dynamic" => true,
					"module_id" => "estelife",
					"title" => GetMessage("ESTELIFE_COMMENTS_TITLE"),
					"items_id" => "menu_estelife_comments",
					"items" => array(
						array(
							"text" => GetMessage("ESTELIFE_COMMENTS_LIST"),
							"dynamic" => true,
							"module_id" => "estelife",
							"title" => GetMessage("ESTELIFE_COMMENTS_LIST_TITLE"),
							"items_id" => "menu_estelife_comments_list",
							"url" => '/bitrix/admin/estelife_comments_list.php?lang='.LANGUAGE_ID,
						),
					)
				),
				array(
					"text" => GetMessage("ESTELIFE_EDUCATION"),
					"dynamic" => true,
					"module_id" => "estelife",
					"title" => GetMessage("ESTELIFE_EDUCATION"),
					"items_id" => "menu_estelife_education",
					"items" => array(
						array(
							"text" => GetMessage("ESTELIFE_EDUCATION_LIST"),
							"dynamic" => true,
							"module_id" => "estelife",
							"title" => GetMessage("ESTELIFE_EDUCATION_LIST"),
							"items_id" => "menu_estelife_education_list",
							"url" => '/bitrix/admin/estelife_education_list.php?lang='.LANGUAGE_ID,
							'more_url'=>array(
								'/bitrix/admin/estelife_education_edit.php?lang='.LANGUAGE_ID,
							)
						),
						array(
							"text" => GetMessage("ESTELIFE_RECEIPT_LIST"),
							"dynamic" => true,
							"module_id" => "estelife",
							"title" => GetMessage("ESTELIFE_RECEIPT_LIST"),
							"items_id" => "menu_estelife_receipt_list",
							"url" => '/bitrix/admin/estelife_receipt_list.php?lang='.LANGUAGE_ID,
							'more_url'=>array(
								'/bitrix/admin/estelife_receipt_edit.php?lang='.LANGUAGE_ID,
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