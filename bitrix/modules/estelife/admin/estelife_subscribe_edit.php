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
IncludeModuleLangFile(__FILE__);

define("HELP_FILE","estelife_list.php");
$ID=isset($_REQUEST['ID']) ?
	intval($_REQUEST['ID']) : 0;

$obSubscribes= \core\database\VDatabase::driver();
$arTypes = array(
	1=>'Клиники',
	2=>'Учебные центры',
	3=>'Новости и статьи',
	10=>'Телемост с Орловой'
);

if(!empty($ID)){
	$obQuery=$obSubscribes->createQuery();
	$obQuery->builder()
		->from('estelife_subscribe_events', 'event')
		->field('event.*')
		->field('owner.email')
		->field('owner.date_send');

	$obJoin=$obQuery
		->builder()
		->join();

	$obJoin->_left()
		->_from('event','owner_id')
		->_to('estelife_subscribe_owners','id','owner');

	$obQuery->builder()
		->filter()
		->_eq('event.id',$ID);

	$arResult=$obQuery
		->select()
		->assoc();
}

if($_SERVER['REQUEST_METHOD']=='POST'){
	$obPost=new VArray($_POST);

	try{
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

			$nUserId = $arUserId['owner_id'];

			$obQueryUser = $obSubscribes->createQuery();
			$obQueryUser->builder()
				->from('estelife_subscribe_owners')
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

						<tr>
							<td width="40%" class="adm-detail-content-cell-l"><?=GetMessage("ESTELIFE_F_EMAIL")?></td>
							<td width="60%" class="adm-detail-content-cell-r"><input type="text" disabled="true" name="email" size="20" maxlength="50" value="<?=$arResult['email'];?>"></td>
						</tr>
						<tr>
							<td width="40%" class="adm-detail-content-cell-l"><?=GetMessage("ESTELIFE_F_DATE_SEND")?></td>
							<td width="60%" class="adm-detail-content-cell-r"><input type="text" disabled="true" name="date_send" size="20" maxlength="50" value="<?=$arResult['date_send'];?>"></td>
						</tr>
						<tr>
							<td width="40%" class="adm-detail-content-cell-l"><?=GetMessage("ESTELIFE_F_ACTIVE")?></td>
							<td width="60%" class="adm-detail-content-cell-r">
								<select name="active" value="">
									<option value="0"<?=($arResult['active']==0 ? ' selected="true"' : '')?>>Нет</option>
									<option value="1"<?=($arResult['active']==1 ? ' selected="true"' : '')?>>Да</option>
								</select>
							</td>
						</tr>
						<tr>
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
