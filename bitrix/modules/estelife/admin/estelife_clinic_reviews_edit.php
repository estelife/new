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

$obSpec= \core\database\VDatabase::driver();
$obElements=new CIBlockElement();

if(!empty($ID)){
	$obQuery = $obSpec->createQuery();
	$obJoin=$obQuery->builder()
		->from('estelife_clinic_reviews','ecr')
		->join();
	$obJoin->_left()
		->_from('ecr', 'problem_id')
		->_to('estelife_clinic_problems', 'id', 'ecp');
	$obJoin->_left()
		->_from('ecr', 'specialist_id')
		->_to('estelife_professionals', 'id', 'ep');
	$obJoin->_left()
		->_from('ep', 'user_id')
		->_to('user', 'ID', 'u');
	$obJoin->_left()
		->_from('ecr', 'clinic_id')
		->_to('estelife_clinics', 'id', 'ec');
	$obJoin->_left()
		->_from('ecr', 'user_id')
		->_to('user', 'ID', 'uu');

	$obQuery->builder()->filter()->_eq('ecr.id',$ID);

	$obFilter=$obQuery->builder()
		->field('ecr.id')
		->field('ecr.date_visit')
		->field('ecr.active')
		->field('ecr.specialist_name')
		->field('ecr.positive_description')
		->field('ecr.negative_description')
		->field('ecr.answer')
		->field('ecr.answer_clinic')
		->field('ep.id', 'user_id')
		->field('ecr.problem_name')
		->field('ecr.date_moderate')
		->field('ec.name', 'clinic_name')
		->field('u.ID', 'spec_id')
		->field('u.NAME', 'name')
		->field('u.LAST_NAME', 'last_name')
		->field('u.LOGIN', 'login')
		->field('uu.NAME', 'user_name')
		->field('uu.LAST_NAME', 'user_last_name')
		->field('uu.LOGIN', 'user_login')
		->field('ecp.title', 'problem')
		->field('ecp.id', 'problem_id');


	$obResult=$obQuery->select();
	$obResult=new CAdminResult(
		$obResult->bxResult(),
		$sTableID
	);

	$arResult['review']=$obResult->Fetch();

}


if($_SERVER['REQUEST_METHOD']=='POST'){
	$obPost=new VArray($_POST);

	try{
		//Создание нового пользователя
		$obQuery = $obSpec->createQuery();
		$obQuery->builder()->from('estelife_clinic_reviews')
			->value('date_visit', trim(htmlentities($obPost->one('date_visit'),ENT_QUOTES,'utf-8')))
			->value('problem_id', intval($obPost->one('problem_id')))
			->value('problem_name', trim(htmlentities($obPost->one('problem_name'),ENT_QUOTES,'utf-8')))
			->value('specialist_id', intval($obPost->one('spec_id')))
			->value('specialist_name', trim(htmlentities($obPost->one('specialist_name'),ENT_QUOTES,'utf-8')))
			->value('positive_description', trim(htmlentities($obPost->one('positive_description'),ENT_QUOTES,'utf-8')))
			->value('negative_description', trim(htmlentities($obPost->one('negative_description'),ENT_QUOTES,'utf-8')))
			->value('answer', trim(htmlentities($obPost->one('answer'),ENT_QUOTES,'utf-8')))
			->value('answer_clinic', trim(htmlentities($obPost->one('answer_clinic'),ENT_QUOTES,'utf-8')));

		if (!empty($ID)){
			$obQuery->builder()->filter()
				->_eq('id',$ID);
			$obQuery->update();
		}

		if(!$obPost->blank('save'))
			LocalRedirect('/bitrix/admin/estelife_clinic_reviews_list.php?lang='.LANGUAGE_ID);
		else
			LocalRedirect('/bitrix/admin/estelife_clinic_reviews_edit.php?lang='.LANGUAGE_ID.'&ID='.$ID);
	}catch(ex\VException $e){
		$arResult['error']=array(
			'text'=>$e->getMessage(),
			'code'=>$e->getCode()
		);
	}
}

//Получение проблем
$obQuery=$obSpec->createQuery();
$obQuery->builder()->from('estelife_clinic_problems');
$arResult['review']['problems']=$obQuery->select()->all();


$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("ESTELIFE_T_BASE"), "TITLE" => GetMessage("ESTELIFE_T_BASE")),
);
$tabControl = new CAdminTabControl("estelife_entry_concreate", $aTabs, true, true);

//===== Тут будем делать сохрпанение и подготовку данных

$APPLICATION->SetTitle(GetMessage('ESTELIFE_HEAD_TITLE'));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

if(!empty($arResult['error']['text'])){
	$arMessages=array(
		$arResult['error']['text'].' ['.$arResult['error']['code'].']'
	);

	if(isset($arResult['error']['fields'])){
		foreach($arResult['error']['fields'] as $sField=>$sError)
			$arMessages[]=GetMessage('ERROR_FIELD_FILL').': '.GetMessage($sError);
	}

	CAdminMessage::ShowOldStyleError(implode('<br />',$arMessages));

	if(!empty($_POST)){
		foreach($_POST as $sKey=>$sValue)
			$arResult['review'][$sKey]=$sValue;
	}

}
?>
	<script type="text/javascript" src="/bitrix/js/estelife/jquery-1.10.2.min.js"></script>
	<script type="text/javascript" src="/bitrix/js/estelife/jquery-ui-1.10.3.custom.min.js"></script>
	<script type="text/javascript" src="/bitrix/js/estelife/jquery-ui.rus.js"></script>
	<script type="text/javascript" src="/bitrix/js/estelife/jquery.damnUploader.js"></script>
	<script type="text/javascript" src="/bitrix/js/estelife/estelife.js"></script>
	<form name="estelife_spec" method="POST" action="<?echo $APPLICATION->GetCurPage()?>" enctype="multipart/form-data">
	<?=bitrix_sessid_post()?>
	<input type="hidden" name="ID" value=<?=$ID?> />
	<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>" />
	<?php
	$tabControl->Begin();
	$tabControl->BeginNextTab()
	?>

	<tr>
		<td width="40%"><?=GetMessage("ESTELIFE_F_ACTIVE")?></td>
		<td width="60%">
			<ul class="estelife-checklist">
				<li>
					<label for="type_1">
						<input type="checkbox" name="active" id="type_1" value="1"<?=(($arResult['review']['active'] == 1) ? ' checked="true"' : '')?> />
					</label>
				</li>
			</ul>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=GetMessage("ESTELIFE_F_USER_NAME")?></td>
		<td width="60%"><input type="text" name="user_full_name" size="60" maxlength="255" value="<?=$arResult['review']['user_name']?>" disabled="disabled"></td>
	</tr>
	<tr>
		<td width="40%"><?=GetMessage("ESTELIFE_F_USER_LAST_NAME")?></td>
		<td width="60%"><input type="text" name="user_last_name" size="60" maxlength="255" value="<?=$arResult['review']['user_last_name']?>" disabled="disabled"></td>
	</tr>
	<tr>
		<td width="40%"><?=GetMessage("ESTELIFE_F_DATE_VISIT")?></td>
		<td width="60%"><input type="text" name="date_visit" size="60" maxlength="255" value="<?=$arResult['review']['date_visit']?>"></td>
	</tr>
	<tr>
		<td width="40%"><?=GetMessage("ESTELIFE_F_CLINIC")?></td>
		<td width="60%"><input type="text" name="clinic_name" size="60" maxlength="255" value="<?=$arResult['review']['clinic_name']?>"  disabled="disabled"></td>
	</tr>
	<tr>
		<td width="40%"><?=GetMessage("ESTELIFE_F_PROBLEM")?></td>
		<td width="60%">
			<select name="problem_id" class="estelife-need-clone">
				<option><?=GetMessage("ESTELIFE_F_SELECT_PROBLEM")?></option>
				<?php if (!empty($arResult['review']['problems'])):?>
					<?php foreach ($arResult['review']['problems'] as $val):?>
						<option value="<?=$val['id']?>" <?php if ($arResult['review']['problem_id'] == $val['id']):?> selected <?php endif?>><?=$val['title']?></option>
					<?php endforeach?>
				<?php endif?>
			</select>
			или
			<input type="text" name="problem_name" size="37" maxlength="37" value="<?=$arResult['review']['problem_name']?>" >
		</td>
	</tr>
	<tr>
		<td width="40%"><?=GetMessage("ESTELIFE_F_SPECIALIST")?></td>
		<td width="60%">
			<input type="hidden" name="user_type_id" value="3" />
			<input type="hidden" name="spec_id" value="<?=$arResult['review']['user_id']?>" />
			<input type="text" name="spec_name" size="30" data-input="spec_id" value="<?=$arResult['review']['last_name']?> <?=$arResult['review']['name']?>" />
			или
			<input type="text" name="specialist_name" size="24" maxlength="24" value="<?=$arResult['review']['specialist_name']?>" >
		</td>
	</tr>
	<tr>
		<td width="40%"><?=GetMessage("ESTELIFE_F_POSITIVE")?></td>
		<td width="60%">
			<textarea name="positive_description" rows="12" style="width:70%"><?=$arResult['review']['positive_description']?></textarea>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=GetMessage("ESTELIFE_F_NEGATIVE")?></td>
		<td width="60%">
			<textarea name="negative_description" rows="12" style="width:70%"><?=$arResult['review']['negative_description']?></textarea>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=GetMessage("ESTELIFE_F_ANSWER")?></td>
		<td width="60%">
			<textarea name="answer" rows="12" style="width:70%"><?=$arResult['review']['answer']?></textarea>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=GetMessage("ESTELIFE_F_ANSWER_CLINIC")?></td>
		<td width="60%">
			<textarea name="answer_clinic" rows="12" style="width:70%"><?=$arResult['review']['answer_clinic']?></textarea>
		</td>
	</tr>
	<?php
	$tabControl->EndTab();
	$tabControl->Buttons(array("disabled"=>false, "back_url"=>(strlen($back_url) > 0 ? $back_url : "estelife_clinic_reviews_list.php?lang=".LANGUAGE_ID)));
	$tabControl->End();
	?>
	</form>
<?php
require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");