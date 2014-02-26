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

$obSections= \core\database\VDatabase::driver();

if(!empty($ID)){

	$obQuery=$obSections->createQuery();

	$obQuery->builder()
		->from('estelife_event_sections','es')
		->field('es.name','name')
		->field('ee.id','event_id')
		->field('ee.short_name','event_name');
	$obJoin=$obQuery->builder()->join();
	$obJoin->_left()->
		_from('es','event_id')->
		_to('estelife_events','id','ee');

	$obQuery->builder()->filter()->_eq('es.id',$ID);
	$arResult['section']=$obQuery->select()->assoc();

	$nEventId = $arResult['section']['event_id'];

	$obQuery->builder()
		->from('estelife_event_halls','eh')
		->field('eh.id','hall_id')
		->field('eh.name','hall_name');
	$obJoin=$obQuery->builder()->join();

	$obQuery->builder()->filter()->_eq('eh.event_id',$nEventId);
	$arResult['halls_all']=$obQuery->select()->all();


	foreach($arResult['halls_all'] as $nKey=>$val){
		$nHallId = $val['hall_id'];

		$obQuery->builder()
			->from('estelife_event_section_halls');

		$obQuery->builder()->filter()->_eq('section_id',$ID)->_eq('hall_id',$nHallId);

		$active=$obQuery->select()->assoc();

		if(!empty($active)){
			$arResult['halls_all'][$nKey]['active'] = 1;
		}else{
			$arResult['halls_all'][$nKey]['active'] = 0;
		}

	}


	$obQuery->builder()
		->from('estelife_event_sections_dates');
	$obQuery->builder()->filter()->_eq('section_id',$ID);


	$arResult['section']['dates']=$obQuery->select()->all();

	foreach($arResult['section']['dates'] as $nKey=> $sDate){

		$sFromDate = strtotime($sDate['time_from']);
		$sFromDate = date('H:i',$sFromDate);

		$sToDate = strtotime($sDate['time_to']);
		$sToDate = date('H:i',$sToDate);

		$arResult['section']['dates'][$nKey]['time_from'] = $sFromDate;
		$arResult['section']['dates'][$nKey]['time_to'] = $sToDate;

	}

	$obQuery=$obSections->createQuery();
	$obQuery->builder()
		->from('estelife_event_section_halls');

	$obQuery->builder()->filter()->_eq('id',$ID);

	$obResult=$obQuery->select();
	$obResult=new CAdminResult(
		$obResult->bxResult(),
		$sTableID
	);

}

if($_SERVER['REQUEST_METHOD']=='POST'){
	$obPost=new VArray($_POST);
	$obError=new ex\VFormException();

	try{
		if($obPost->blank('name'))
			$obError->setFieldError('NAME_NOT_FILL','name');

		if($obPost->blank('event_id'))
			$obError->setFieldError('EVENT_NOT_FILL','event_id');

		if($obPost->blank('halls_id'))
			$obError->setFieldError('HALL_NOT_FILL','event_id');

		$obError->raise();

		$obQueryBase = $obSections->createQuery();
		$obQueryDate = $obSections->createQuery();
		$obQueryHalls = $obSections->createQuery();
		$obQueryDateRemove = $obSections->createQuery();
		$obQueryBase->builder()->from('estelife_event_sections')
			->value('name', trim(htmlentities($obPost->one('name'),ENT_QUOTES,'utf-8')))
			->value('number', intval($obPost->one('number'),ENT_QUOTES,'utf-8'))
			->value('event_id', intval($obPost->one('event_id')));

		$arPostHalls = $obPost->one('halls_id');

		if (!empty($ID)) {
			$obQueryBase->builder()->filter()
				->_eq('id',$ID);
			$obQueryBase->update();
			$idEntr = $ID;
		} else {
			$ID = $obQueryBase->insert()->insertId();
		}

		$obQueryDateRemove->builder()
			->from('estelife_event_sections_dates')->filter()
			->_eq('section_id', $ID);

		$obQueryDateRemove->delete();

		$obQueryHalls->builder()
			->from('estelife_event_section_halls')->filter()
			->_eq('section_id', $ID);

		$obQueryHalls->delete();

		if (!empty($arPostHalls)) {
			foreach($arPostHalls as $nVal){
				$obQueryHalls->builder()->from('estelife_event_section_halls')
					->value('hall_id',$nVal)
					->value('section_id',$ID);
				$obQueryHalls->insert()->insertId();
			}
		}


		if($arDates=$obPost->one('date')){
			$arTimeFrom=$obPost->one('time_from',array());
			$arTimeTo=$obPost->one('time_to',array());


			foreach($arDates as $nKey=>$sDate){

				if(empty($sDate))
					continue;

				$sDate =date('Y-m-d',strtotime($sDate));

				$obQueryDate->builder()
					->from('estelife_event_sections_dates')->filter()
					->_eq('section_id', $ID);

				$obQueryDate->builder()
					->value('date',$sDate)
					->value('section_id',$ID);


				if(!empty($arTimeFrom[$nKey]))
					$obQueryDate->builder()->value('time_from',$arTimeFrom[$nKey]);

				if(!empty($arTimeTo[$nKey]))
					$obQueryDate->builder()->value('time_to',$arTimeTo[$nKey]);

				$obQueryDate->insert()->affected();
			}
		}


		if(!$obPost->blank('save'))
			LocalRedirect('/bitrix/admin/estelife_event_sections_list.php?lang='.LANGUAGE_ID);
		else
			LocalRedirect('/bitrix/admin/estelife_event_sections_edit.php?lang='.LANGUAGE_ID.'&ID='.$ID);
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
	array("DIV" => "edit1", "TAB" => GetMessage("ESTELIFE_T_BASE"), "ICON" => "estelife_r_base", "TITLE" => GetMessage("ESTELIFE_T_BASE")),
	array("DIV" => "edit4", "TAB" => GetMessage("ESTELIFE_T_CALENDAR"), "ICON" => "estelife_r_base", "TITLE" => GetMessage("ESTELIFE_T_CALENDAR")),
);
$tabControl = new CAdminTabControl("estelife_event_sections_".$ID, $aTabs, true, true);



//===== Тут будем делать сохрпанение и подготовку данных

$APPLICATION->SetTitle(GetMessage('ESTELIFE_CREATE_TITLE'));
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
			$arResult['section'][$sKey]=$sValue;
	}

}

?>

	<script type="text/javascript" src="/bitrix/js/estelife/jquery-1.10.2.min.js"></script>
	<script type="text/javascript" src="/bitrix/js/estelife/jquery-ui-1.10.3.custom.min.js"></script>
	<script type="text/javascript" src="/bitrix/js/estelife/estelife.js"></script>

	<form name="estelife_event_sections" method="POST" action="<?echo $APPLICATION->GetCurPage()?>" enctype="multipart/form-data">
	<?=bitrix_sessid_post()?>
	<input type="hidden" name="ID" value=<?=$ID?> />
	<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>" />
	<input type="hidden" name="types[]" value="3" />
	<?php
	$tabControl->Begin();
	$tabControl->BeginNextTab()
	?>
	<tr class="adm-detail-required-field">
		<td width="40%"><?=GetMessage("ESTELIFE_F_NAME")?></td>
		<td width="60%"><input type="text" name="name" size="40" maxlength="255" value="<?=$arResult['section']['name']?>"></td>
	</tr>

	<tr>
		<td width="40%"><?=GetMessage("ESTELIFE_F_NUMBER")?></td>
		<td width="60%">
			<input type="text" name="number" size="40" value="<?=$arResult['section']['number']?>" />
		</td>
	</tr>

	<tr class="adm-detail-required-field">
		<td width="40%"><?=GetMessage("ESTELIFE_F_EVENT")?></td>
		<td width="60%">
			<input type="hidden" name="event_type_id" value="3" />
			<input type="hidden" name="event_id" value="<?=$arResult['section']['event_id']?>" />
			<input type="text" name="event_name" size="40" data-input="event_id" value="<?=$arResult['section']['event_name']?>" />
		</td>
	</tr>

	<tr class="adm-detail-required-field">
		<td width="40%" class="adm-detail-content-cell-l"><?=GetMessage("ESTELIFE_F_HALLS")?></td>
		<td width="60%" class="adm-detail-content-cell-r">
			<select name="halls_id[]" id="halls" style="width: 340px;" multiple>
				<? if(!empty($arResult['halls_all'])){
					foreach($arResult['halls_all'] as $val){ ?>
					<option value="<?=$val['hall_id'];?>" <? if($val['active'] == 1){ ?>selected <?}?>><?=$val['hall_name'];?></option>
				<?	}
						} ?>
			</select>
		</td>
	</tr>

	<?
	$tabControl->BeginNextTab()
	?>

	<tr>
		<td colspan="2">
			<div class="event-dates">
				<ul>
					<?php if(!empty($arResult['section']['dates'])):?>
						<?php foreach($arResult['section']['dates'] as $arValue): ?>
							<li>
								<input type="text" name="date[]" value="<?=$arValue['date']?>" class="date_select" /> c <input type="text" value="<?=$arValue['time_from']?>" name="time_from[]" class="time" size="5" /> по <input type="text" class="time" size="5" name="time_to[]" value="<?=$arValue['time_to']?>" />
								<a href="#" class="estelife-btn adm-btn adm-btn-delete estelife-delete"></a>
							</li>
						<?php endforeach; ?>
					<?php endif;?>
					<li>
						<input type="text" name="date[]" value="" class="date_select" /> c <input type="text" value="" name="time_from[]" class="time" size="5" /> по <input type="text" class="time" size="5" name="time_to[]" value="" />
						<a href="#" class="estelife-btn adm-btn adm-btn-save">&crarr;</a>
					</li>
				</ul>
			</div>
			<div class="calendar_l">
				<div class="calendar_l_in">
					<a href="#" class="ar l" id="lc">август<span> 2013 </span><i></i></a>
					<div class="cal l">
						<div id="datepicker1"></div>
					</div>
					<div class="cal r">
						<div id="datepicker2"></div>
					</div>
					<a href="#" class="ar r" id="rc">ноябрь<span> 2013 </span><i></i></a>
				</div>
			</div>
		</td>
	</tr>

	<?php
	$tabControl->EndTab();
	$tabControl->Buttons(array("disabled"=>false, "back_url"=>(strlen($back_url) > 0 ? $back_url : "estelife_event_sections_list.php?lang=".LANGUAGE_ID)));
	$tabControl->End();
	?>
	</form>
<?php
require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");

