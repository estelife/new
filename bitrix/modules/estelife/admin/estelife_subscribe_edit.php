<?php
use core\exceptions as ex;
use core\types\VArray;
use core\types\VString;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/estelife/prolog.php");

ClearVars();
$FORM_RIGHT = $APPLICATION->GetGroupRight("estelife");

if($FORM_RIGHT<="D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

CModule::IncludeModule("estelife");
CModule::IncludeModule('iblock');

IncludeModuleLangFile(__FILE__);

define("HELP_FILE","estelife_list.php");
$ID=isset($_REQUEST['ID']) ?
	intval($_REQUEST['ID']) : 0;

$obSubscribes= \core\database\VDatabase::driver();
$obElements=new CIBlockElement();

if(!empty($ID)){

	$obQuery=$obSubscribes->createQuery();
	$obQuery->builder()->from('estelife_subscribe_events', 'se');
	$obJoin=$obQuery->builder()->join();
	$obJoin->_left()
		->_from('se','subscribe_user_id')
		->_to('estelife_subscribe_user','user_id','su');

	$obQuery->builder()->filter()->_eq('id',$ID);

	$obResult=$obQuery->select();
	$obResult=new CAdminResult(
		$obResult->bxResult(),
		$sTableID
	);

	$arTypes = array(
		1=>'Клиники',
		2=>'Учебные центры',
	);

	$arResult=$obResult->Fetch();

}

if($_SERVER['REQUEST_METHOD']=='POST'){
	$obPost=new VArray($_POST);
	$obError=new ex\VFormException();

	try{

		//не срабатывает
		if($obPost->blank('email'))
			$obError->setFieldError('NAME_NOT_FILL','email');

		$obError->raise();


		$obQuery = $obSubscribes->createQuery();
		$obQuery->builder()->from('estelife_subscribe_events')
			//->value('email', trim(htmlentities($obPost->one('email'),ENT_QUOTES,'utf-8')))
			->value('event_active', intval($obPost->one('active')))
			->value('type', intval($obPost->one('type')));





		if (!empty($ID)){

			$obQueryUserId = $obSubscribes->createQuery();
			$obQueryUserId->builder()->from('estelife_subscribe_events')
				->filter()
				->_eq('id', $ID);
			$arUserId = $obQueryUserId->select()->assoc();

			$nUserId = $arUserId['subscribe_user_id'];

			$obQueryUser = $obSubscribes->createQuery();
			$obQueryUser->builder()->from('estelife_subscribe_user')
				->value('email', trim(htmlentities($obPost->one('email'),ENT_QUOTES,'utf-8')));

			$obQueryUser->builder()->filter()
				->_eq('user_id',$nUserId);

			$obQuery->builder()->filter()
				->_eq('id',$ID);
			$obQuery->update();
			$obQueryUser->update();
			$idSub = $ID;
		}else{
			//$idPill = $obQuery->insert()->insertId();
		}


		if(!$obPost->blank('save'))
			LocalRedirect('/bitrix/admin/estelife_subscribe_list.php?lang='.LANGUAGE_ID);
		else
			LocalRedirect('/bitrix/admin/estelife_subscribe_edit.php?lang='.LANGUAGE_ID.'&ID='.$idSub);
	}catch(ex\VFormException $e){
		$arResult['error']=array(
			'text'=>$e->getMessage(),
			'code'=>11
		);
		$arResult['error']['fields']=$e->getFieldErrors();
	}catch(ex\VException $e){
		$arResult['error']=array(
			'text'=>$e->getMessage(),
			'code'=>$e->getCode()
		);
	}
}


$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("ESTELIFE_T_BASE"),/* "ICON" => "estelife_r_base", */"TITLE" => GetMessage("ESTELIFE_T_BASE")),
);
$tabControl = new CAdminTabControl("estelife_subscribe_concreate_".$ID, $aTabs, true, true);



//===== Тут будем делать сохрпанение и подготовку данных

$APPLICATION->SetTitle(GetMessage('ESTELIFE_CREATE_TITLE'));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

?>

<script type="text/javascript" src="/bitrix/js/estelife/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="/bitrix/js/estelife/jquery-ui-1.10.3.custom.min.js"></script>
<script type="text/javascript" src="/bitrix/js/estelife/estelife.js"></script>

<form name="estelife_subscribe" method="POST" action="/bitrix/admin/estelife_subscribe_edit.php" enctype="multipart/form-data">
	<?=bitrix_sessid_post()?>
	<input type="hidden" name="ID" value=<?=$ID?> />
	<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>" />
	<?php
		$tabControl->Begin();
		$tabControl->BeginNextTab();
	?>

						<tr class="adm-detail-required-field">
							<td width="40%" class="adm-detail-content-cell-l"><?=GetMessage("ESTELIFE_F_EMAIL")?></td>
							<td width="60%" class="adm-detail-content-cell-r"><input type="text" name="email" size="20" maxlength="50" value="<?=$arResult['email'];?>"></td>
						</tr>
						<tr class="adm-detail-required-field">
							<td width="40%" class="adm-detail-content-cell-l"><?=GetMessage("ESTELIFE_F_ACTIVE")?></td>
							<td width="60%" class="adm-detail-content-cell-r">
								<select name="active" value="">
									<? if($arResult['event_active']== 0){ ?>
									<option value="0">Нет</option>
									<option value="1">Да</option>
									<? }else{ ?>
									<option value="1">Да</option>
									<option value="0">Нет</option>
									<? } ?>
								</select>
							</td>
						</tr>
						<tr class="adm-detail-required-field">
							<td width="40%" class="adm-detail-content-cell-l"><?=GetMessage("ESTELIFE_F_TYPE_SELECT")?></td>
							<td width="60%" class="adm-detail-content-cell-r">
								<select name="type" value="">
									<? foreach($arTypes as $key=>$value){ ?>
										<option value="<?=$key;?>" <? if($arResult['type'] == $key){ ?>selected<? } ?>><?=$value;?></option>
									<? } ?>

								</select>
							</td>
						</tr>

	<?php
		$tabControl->EndTab();
		$tabControl->Buttons(array("disabled"=>false, "back_url"=>(strlen($back_url) > 0 ? $back_url : "estelife_subscribe_list.php?lang=".LANGUAGE_ID)));
		$tabControl->End();
	?>
</form>
<?php
require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
