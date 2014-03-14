<?php
/**
 * Вероятно я забыл оставить описание. Пишите на почту, дам комментарий.
 * @since 04.10.13
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 */
global $MESS;
$PathInstall = str_replace("\\", "/", __FILE__);
$PathInstall = substr($PathInstall, 0, strlen($PathInstall)-strlen("/index.php"));
IncludeModuleLangFile($PathInstall."/install.php");

if(class_exists("estelife")) return;

class estelife extends CModule {
	var $MODULE_ID='estelife';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_GROUP_RIGHTS='Y';

	function estelife(){
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");

		if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
		{
			$this->MODULE_VERSION = $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		}
		else
		{
			$this->MODULE_VERSION = ESTELIFE_VERSION;
			$this->MODULE_VERSION_DATE = ESTELIFE_VERSION_DATE;
		}

		$this->MODULE_NAME = GetMessage("ESTELIFE_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("ESTELIFE_MODULE_DESCRIPTION");
	}

	function DoInstall(){
		global $DB, $DOCUMENT_ROOT, $APPLICATION, $step, $errors, $public_dir;
		$ESTELIFE_RIGHT = $APPLICATION->GetGroupRight("estelife");

		if($ESTELIFE_RIGHT>="W"){
			$errors = false;

			$this->InstallFiles();
			$this->InstallDB();

			$APPLICATION->IncludeAdminFile(
				GetMessage("ESTELIFE_INSTALL_TITLE"),
				$DOCUMENT_ROOT."/bitrix/modules/estelife/install/step2.php"
			);
		}
	}

	function InstallDB(){
		global $APPLICATION, $DB, $errors;

		$errors=false;
		$errors=$DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/estelife/install/db/".strtolower($DB->type)."/install.sql");

		if(!empty($errors)){
			$APPLICATION->ThrowException(implode('', $errors));
			return false;
		}

		RegisterModule("estelife");
		return true;
	}

	function InstallFiles(){
		if($_ENV["COMPUTERNAME"]!='BX'){
			CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/estelife/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin", true);
			CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/estelife/install/images", $_SERVER["DOCUMENT_ROOT"]."/bitrix/images/estelife", true, true);
			CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/estelife/install/js", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js", true, true);
			CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/estelife/install/themes", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes", true, true);
			CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/estelife/install/components", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);
			CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/estelife/install/tools", $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools");
		}

		return true;
	}

	function InstallEvents(){
		return true;
	}

	function DoUninstall(){
//		global $DB, $APPLICATION, $step, $errors;
//		$ESTELIFE_RIGHT=$APPLICATION->GetGroupRight("estelife");
//
//		if($ESTELIFE_RIGHT>="W"){
//			$step = IntVal($step);
//			if($step<2){
//				$APPLICATION->IncludeAdminFile(GetMessage("ESTELIFE_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/estelife/install/unstep1.php");
//			}elseif($step==2){
//				$errors = false;
//
//				$this->UnInstallDB(array(
//					"savedata" => $_REQUEST["savedata"],
//				));
//
//				$this->UnInstallFiles(array(
//					"savedata" => $_REQUEST["savedata"],
//				));
//
//				$APPLICATION->IncludeAdminFile(GetMessage("ESTELIFE_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/estelife/install/unstep2.php");
//			}
//		}
	}

	function UnInstallDB($arParams = Array()){
//		global $APPLICATION, $DB, $errors;
//
//		if(!array_key_exists("savedata", $arParams) || $arParams["savedata"] != "Y"){
//			$errors=false;
//			$errors=$DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/estelife/install/db/".strtolower($DB->type)."/uninstall.sql");
//
//			if(!empty($errors)){
//				$APPLICATION->ThrowException(implode("", $errors));
//				return false;
//			}
//		}
//
//		COption::RemoveOption("estelife");
//		UnRegisterModule("estelife");
//
//		return true;
	}

	function UnInstallFiles($arParams=array()){
//		global $DB;
//
//		if(array_key_exists("savedata", $arParams) && $arParams["savedata"]!="Y"){
//			$db_res = $DB->Query("SELECT ID FROM b_file WHERE MODULE_ID = 'estelife'");
//			while($arRes = $db_res->Fetch())
//				CFile::Delete($arRes["ID"]);
//		}
//
//		// Delete files
//		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/estelife/install/admin/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
//		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/estelife/install/themes/.default/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default");//css
//		DeleteDirFilesEx("/bitrix/themes/.default/icons/estelife/");//icons
//		DeleteDirFilesEx("/bitrix/images/estelife/");//images
//		DeleteDirFilesEx("/bitrix/js/estelife/");//javascript
//
//		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/estelife/install/tools/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools/");
//
//		// delete temporary template files - for old template system
//		DeleteDirFilesEx(BX_PERSONAL_ROOT."/tmp/estelife/");
//
//		return true;
	}

	function UnInstallEvents(){
		return true;
	}

	function GetModuleRightList()
	{
		global $MESS;
		$arr = array(
			"reference_id" => array("D","E","F","W"),
			"reference" => array(
				"[D] ".GetMessage("ESTELIFE_DENIED"),
				"[E] ".GetMessage("ESTELIFE_OPENED"),
				"[F] ".GetMessage("ESTELIFE_PRIVATE"),
				"[W] ".GetMessage("ESTELIFE_FULL"))
		);
		return $arr;
	}
}