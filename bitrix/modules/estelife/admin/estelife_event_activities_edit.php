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

$obActiv= \core\database\VDatabase::driver();

$obQuery=\core\database\VDatabase::driver()->createQuery();
$obQuery->builder()
	->from('estelife_activity_types')
	->field('id')
	->field('name');
$arFilterData['types']=$obQuery->select()->all();


if(!empty($ID)){

	$obQuery=$obActiv->createQuery();
	$obQuery->builder()->from('estelife_event_activities','ea');
	$obJoin=$obQuery->builder()->join();
	$obJoin->_left()
		->_from('ea', 'type_id')
		->_to('estelife_activity_types', 'id', 'type');
	$obJoin->_left()
		->_from('ea','hall_id')
		->_to('estelife_event_halls', 'id', 'eh');
	$obJoin->_left()
		->_from('ea','section_id')
		->_to('estelife_event_sections', 'id', 'es');
	$obJoin->_left()->
		_from('ea','event_id')->
		_to('estelife_events','id','ee');
	$obQuery->builder()
		->field('ea.id','id')
		->field('ea.name','name')
		->field('ea.short_description', 'short_description')
		->field('ea.full_description', 'full_description')
		->field('ea.with_video','video')
		->field('ea.date','date')
		->field('ea.time_from','time_from')
		->field('ea.time_to','time_to')
		->field('ea.duration','duration')
		->field('type.name','type')
		->field('type.id','type_id')
		->field('eh.name','hall')
		->field('eh.id','hall_id')
		->field('es.name','section')
		->field('ea.event_id','event_id')
		->field('ee.short_name','event_name')
		->field('es.id','section_id')->filter()->_eq('ea.id',$ID);;


	$obResult=$obQuery->select();
	$obResult=new CAdminResult(
		$obResult->bxResult(),
		$sTableID
	);

	$arResult['activ']=$obResult->Fetch();


	$nEventId = $arResult['activ']['event_id'];

	$obQuery->builder()
		->from('estelife_event_sections')
		->field('id')
		->field('name')->filter()->_eq('event_id',$nEventId);

	$arFilterData['sections']=$obQuery->select()->all();


	$obQuery->builder()
		->from('estelife_event_halls')
		->field('id')
		->field('name')->filter()->_eq('event_id',$nEventId);;
	$arFilterData['halls']=$obQuery->select()->all();

	$nVideo = $arResult['activ']['video'];
	$sDate = $arResult['activ']['date'];
	$sDate = date('d-m-Y',strtotime($sDate));
	$sDate = str_replace('-','.',$sDate);
	$nType = $arResult['activ']['type_id'];
	$nHall = $arResult['activ']['hall_id'];
	$nSection = $arResult['activ']['section_id'];
	$sTimeFrom = date('H:i',strtotime($arResult['activ']['time_from']));
	$sTimeTo = date('H:i',strtotime($arResult['activ']['time_to']));

}



if($_SERVER['REQUEST_METHOD']=='POST'){
	$obPost=new VArray($_POST);
	$obError=new ex\VFormException();

	try{

		if(!empty($_POST)){
			$arResult['activ']['name'] = $_POST['name'];
			$arResult['activ']['short_description'] = $_POST['short_description'];
			$arResult['activ']['full_description'] = $_POST['full_description'];
			$nVideo = $_POST['video'];
			$sDate = $_POST['date'];
			$sTimeFrom = $_POST['time_from'];
			$sTimeTo = $_POST['time_to'];
			$arResult['activ']['duration'] = $_POST['duration'];
			$nType = $_POST['type_id'];
			$arResult['activ']['event_id'] = $_POST['event_id'];
			$nSection = $_POST['section_id'];
			$nHall = $_POST['hall_id'];
		}

		if($obPost->blank('name'))
			$obError->setFieldError('NOT_NAME','name');

		if($obPost->blank('short_description'))
			$obError->setFieldError('NOT_SHORT_DESC','short_description');

		if($obPost->blank('full_description'))
			$obError->setFieldError('NOT_FULL_DESC','full_description');

		if($obPost->blank('date'))
			$obError->setFieldError('NOT_DATE','date');

		if($obPost->blank('time_from'))
			$obError->setFieldError('NOT_TIME_FROM','time_from');

		if($obPost->blank('time_to'))
			$obError->setFieldError('NOT_TIME_TO','time_to');

		if($obPost->blank('hall_id'))
			$obError->setFieldError('NOT_HALL','hall_id');

		$sPostTimeFrom =  $obPost->one('time_from');
		$sPostTimeTo =  $obPost->one('time_to');

		$isPointsFrom = strpos($sPostTimeFrom, ':');
		$isPointsTo = strpos($sPostTimeTo, ':');

		if(empty($isPointsFrom)){
			$sPostTimeFrom = ''.$sPostTimeFrom.':00';
		}

		if(empty($isPointsTo)){
			$sPostTimeTo = ''.$sPostTimeTo.':00';
		}

		$obError->raise();

		$sPostDate = date('Y-m-d',strtotime(str_replace('.','-',$obPost->one('date'))));

		$obQuery = $obActiv->createQuery();
		$obQuery->builder()->from('estelife_event_activities')
			->value('name', trim(htmlentities($obPost->one('name'),ENT_QUOTES,'utf-8')))
			->value('short_description', trim(htmlentities($obPost->one('short_description'),ENT_QUOTES,'utf-8')))
			->value('full_description', trim(htmlentities($obPost->one('full_description'),ENT_QUOTES,'utf-8')))
			->value('with_video', intval($obPost->one('video')))
			->value('date', $sPostDate)
			->value('time_from',$sPostTimeFrom)
			->value('time_to',$sPostTimeTo)
			->value('duration',intval($obPost->one('duration')))
			->value('type_id',intval($obPost->one('type_id')))
			->value('hall_id',intval($obPost->one('hall_id')));

		if(!$obPost->blank('section_id'))
			$obQuery->builder()->value('section_id',intval($obPost->one('section_id')));

		if(!$obPost->blank('event_id'))
			$obQuery->builder()->value('event_id',intval($obPost->one('event_id')));


		if (!empty($ID)){
			$obQuery->builder()->filter()
				->_eq('id',$ID);
			$obQuery->update();
			$idEntr = $ID;
		}else{
			$idEntr = $obQuery->insert()->insertId();
		}


		if(!$obPost->blank('save'))
			LocalRedirect('/bitrix/admin/estelife_event_activities_list.php?lang='.LANGUAGE_ID);
		else
			LocalRedirect('/bitrix/admin/estelife_event_activities_edit.php?lang='.LANGUAGE_ID.'&ID='.$idEntr);
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
$tabControl = new CAdminTabControl("estelife_entry_concreate_".$ID, $aTabs, true, true);

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
			$arResult['event'][$sKey]=$sValue;
	}

}
?>
	<script type="text/javascript" src="/bitrix/js/estelife/jquery-1.10.2.min.js"></script>
	<script type="text/javascript" src="/bitrix/js/estelife/jquery-ui-1.10.3.custom.min.js"></script>
	<script type="text/javascript" src="/bitrix/js/estelife/jquery-ui.rus.js"></script>
	<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyAZfcZn-KLKm52_chZk22TGMdooeDvMYfI&sensor=false"></script>
	<script type="text/javascript" src="/bitrix/js/estelife/vMapStyle.js"></script>
	<script type="text/javascript" src="/bitrix/js/estelife/vMap.js"></script>
	<script type="text/javascript" src="/bitrix/js/estelife/jquery.damnUploader.js"></script>
	<script type="text/javascript" src="/bitrix/js/estelife/estelife.js"></script>

	<form name="estelife_subscribe" method="POST" action="/bitrix/admin/estelife_event_activities_edit.php" enctype="multipart/form-data">
		<?=bitrix_sessid_post()?>
		<input type="hidden" name="ID" value=<?=$ID?> />
		<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>" />
		<?php
			$tabControl->Begin();
			$tabControl->BeginNextTab();
		?>

		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_NAME")?></td>
			<td width="60%">
				<input type="text" name="name" size="60" maxlength="255" value="<?=$arResult['activ']['name']?>">
			</td>
		</tr>

		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_SHORT_DESCRIPTION")?></td>
			<td width="60%">
				<input type="text" name="short_description" size="60" maxlength="255" value="<?=$arResult['activ']['short_description']?>">
			</td>
		</tr>

		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_FULL_DESCRIPTION")?></td>
			<td width="60%">
				<textarea cols="59" name="full_description" rows="10"><?=$arResult['activ']['full_description'];?></textarea>
			</td>
		</tr>

		<tr class="adm-detail-required-field">
			<td width="40%" class="adm-detail-content-cell-l"><?=GetMessage("ESTELIFE_F_VIDEO")?></td>
			<td width="60%" class="adm-detail-content-cell-r">
				<select name="video">
					<option value="0"<?=(0==$nVideo ? ' selected="true"' : '')?>>Нет</option>
					<option value="1"<?=(1==$nVideo ? ' selected="true"' : '')?>>Да</option>
				</select>
			</td>
		</tr>

		<tr class="adm-detail-required-field">
			<td width="40%"><?=GetMessage("ESTELIFE_F_DATE")?></td>
			<td width="60%"><div class="event-dates"><?echo CAdminCalendar::CalendarDate("date", $sDate, 19, false)?> c <input type="text" value="<?=$sTimeFrom;?>" name="time_from" class="time" size="5" /> по <input type="text" class="time" size="5" name="time_to" value="<?=$sTimeTo;?>" /></div></td>
		</tr>

		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_DURATION")?></td>
			<td width="60%">
				<input type="text" name="duration" size="5" maxlength="25" value="<?=$arResult['activ']['duration']?>"> часов
			</td>
		</tr>

		<tr class="adm-detail-required-field">
			<td width="40%" class="adm-detail-content-cell-l"><?=GetMessage("ESTELIFE_F_TYPE")?></td>
			<td width="60%" class="adm-detail-content-cell-r">
				<select name="type_id">
					<?php if(!empty($arFilterData['types'])): ?>
						<?php foreach($arFilterData['types'] as $nKey=>$arType): ?>
							<option value="<?=$arType['id']?>"<?=($arType['id']==$nType ? ' selected="true"' : '')?>><?=$arType['name']?></option>
						<?php endforeach; ?>
					<?php endif; ?>
				</select>
			</td>
		</tr>
		<tr class="adm-detail-required-field">
			<td width="40%"><?=GetMessage("ESTELIFE_F_EVENT")?></td>
			<td width="60%">
				<input type="hidden" name="event_type_id" value="3" />
				<input type="hidden" name="event_id" value="<?=$arResult['activ']['event_id']?>" />
				<input type="text" name="event_name" size="30" data-input="event_id" value="<?=$arResult['activ']['event_name']?>" />
			</td>
		</tr>
		<tr class="adm-detail-required-field">
			<td width="40%" class="adm-detail-content-cell-l"><?=GetMessage("ESTELIFE_F_SECTION")?></td>
			<td width="60%" class="adm-detail-content-cell-r">
				<select name="section_id" id="sections" style="width: 260px;">
					<?php if(!empty($arFilterData['sections'])): ?>
						<?php foreach($arFilterData['sections'] as $nKey=>$arSection): ?>
							<option value="<?=$arSection['id']?>"<?=($arSection['id']==$nSection ? ' selected="true"' : '')?>><?=$arSection['name']?></option>
						<?php endforeach; ?>
					<?php endif; ?>
				</select>
			</td>
		</tr>
		<tr class="adm-detail-required-field">
			<td width="40%" class="adm-detail-content-cell-l"><?=GetMessage("ESTELIFE_F_HALL")?></td>
			<td width="60%" class="adm-detail-content-cell-r">
				<select name="hall_id" id="halls">
					<?php if(!empty($arFilterData['halls'])): ?>
						<?php foreach($arFilterData['halls'] as $nKey=>$arHall): ?>
							<option value="<?=$arHall['id']?>"<?=($arHall['id']==$nHall ? ' selected="true"' : '')?>><?=$arHall['name']?></option>
						<?php endforeach; ?>
					<?php endif; ?>
				</select>
			</td>
		</tr>

		<?php
		$tabControl->EndTab();
		$tabControl->Buttons(array("disabled"=>false, "back_url"=>(strlen($back_url) > 0 ? $back_url : "estelife_event_activities_list.php?lang=".LANGUAGE_ID)));
		$tabControl->End();
		?>
	</form>
<?php
require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");