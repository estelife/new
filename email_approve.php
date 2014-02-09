<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("EsteLife.RU - информационный портал о косметологии и пластической хирургии");
$APPLICATION->SetPageProperty("description", "EsteLife.RU - информационный портал о косметологии и пластической хирургии");
$APPLICATION->SetPageProperty("keywords", "косметология, пластическая хирургия");
$APPLICATION->SetPageProperty("title", "EsteLife.RU - информационный портал о косметологии и пластической хирургии");


$sFileAddr=dirname(__FILE__);
$sFileAddr=str_replace('/cron','',$sFileAddr);
$_SERVER["DOCUMENT_ROOT"] = $sFileAddr;

$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
@set_time_limit(0);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/estelife/prolog.php");

CModule::IncludeModule('estelife');
CModule::IncludeModule('iblock');


const FIRST=1;

$obData = \core\database\VDatabase::driver();

$obQueryUsers=$obData->createQuery();
$obQueryUsers->builder()->from('estelife_subscribe_user')
	->filter()
	->_eq('active',0);
$arUsers = $obQueryUsers->select()->all();


$nApprove = 0;
$sHashLink = $_GET['link'];

foreach($arUsers as $usKey=>$arUser){
	$sHashUserLink = md5($arUser['email'].$arUser['date_last_send']);

	if($sHashLink == $sHashUserLink){
		$obQuery->builder()->from('estelife_subscribe_user')
			->value('active', 1);

		$obQuery->builder()->filter()
			->_eq('user_id',$arUser['user_id']);
		//$obQuery->update();

		$nApprove = 1;
	}
}
echo $nApprove;
if($nApprove != 1){
	?>
	<div>Ошибка!</div>
<? }else{ ?>
	<div>Email успешно подтвержден!</div>
<? } ?>



<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>