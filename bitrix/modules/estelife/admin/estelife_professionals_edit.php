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

$obQuery = $obSpec->createQuery();

$obQuery->builder()
	->from('user');

$arUsers = $obQuery->select()->all();

if(!empty($ID)){

	$obQuery = $obSpec->createQuery();

	$obJoin=$obQuery->builder()
		->from('estelife_professionals','ep')
		->join();
	$obJoin->_left()
		->_from('ep','country_id')
		->_to('iblock_element','ID','ecn')
		->_cond()
		->_eq('ecn.IBLOCK_ID',15);
	$obJoin->_left()
		->_from('ep','city_id')
		->_to('iblock_element','ID','ect')
		->_cond()
		->_eq('ect.IBLOCK_ID',16);

	$obQuery->builder()->filter()->_eq('ep.id',$ID);

	$obFilter=$obQuery->builder()
		->sort($by,$order)
		->field('ep.id','id')
		->field('ep.user_id','user_id')
		->field('ep.country_id','country_id')
		->field('ep.image_id','image_id')
		->field('ecn.NAME','country')
		->field('ep.city_id','city_id')
		->field('ect.NAME','city')
		->field('ep.short_description','short_description')
		->field('ep.full_description','full_description')
		->filter();


	$obResult=$obQuery->select();
	$obResult=new CAdminResult(
		$obResult->bxResult(),
		$sTableID
	);

	$arResult['spec']=$obResult->Fetch();

	$nUserId = $arResult['spec']['user_id'];

	$obQuery->builder()
		->from('user')
		->field('NAME')->filter()->_eq('ID',$nUserId);
	$arFilterData['user']=$obQuery->select()->Fetch();

	$sUserName =$arFilterData['user']['NAME'];

}



if($_SERVER['REQUEST_METHOD']=='POST'){
	$obPost=new VArray($_POST);
	$obError=new ex\VFormException();

	try{
		if($obPost->blank('user_id'))
			$obError->setFieldError('NOT_USER','user_id');

		$obError->raise();


		$obQuery = $obSpec->createQuery();
		$obQuery->builder()->from('estelife_professionals')
			->value('user_id', $obPost->one('user_id'))
			->value('short_description', trim(htmlentities($obPost->one('short_description'),ENT_QUOTES,'utf-8')))
			->value('full_description', trim(htmlentities($obPost->one('full_description'),ENT_QUOTES,'utf-8')));


		if(!empty($_FILES['photo'])){
			$arImage=$_FILES['photo'];
			$arImage['old_file']=$obRecord['photo'];
			$arImage['module']='estelife';
			$arImage['del']=$small_photo_del;

			if(strlen($arImage["name"])>0 || strlen($arImage["del"])>0){
				$nImageId=CFile::SaveFile($arImage,"estelife");
				$obQuery->builder()
					->value('image_id', intval($nImageId));
			}
		}

		if(!$obPost->blank('country_id'))
			$obQuery->builder()->value('country_id', intval($obPost->one('country_id',0)));

		if(!$obPost->blank('city_id'))
			$obQuery->builder()->value('city_id', intval($obPost->one('city_id',0)));


		if (!empty($ID)){

			if(isset($_POST['photo_del'])&& $_POST['photo_del'] == 'Y'){
				$obQuery->builder()->value('image_id', intval(0));
			}

			$obQuery->builder()->filter()
				->_eq('id',$ID);
			$obQuery->update();
			$idEntr = $ID;
		}else{
			$idPill = $obQuery->insert()->insertId();
		}


		if(!$obPost->blank('save'))
			LocalRedirect('/bitrix/admin/estelife_professionals_list.php?lang='.LANGUAGE_ID);
		else
			LocalRedirect('/bitrix/admin/estelife_professionals_edit.php?lang='.LANGUAGE_ID.'&ID='.$idEntr);
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

	<form name="estelife_subscribe" method="POST" action="/bitrix/admin/estelife_professionals_edit.php" enctype="multipart/form-data">
		<?=bitrix_sessid_post()?>
		<input type="hidden" name="ID" value=<?=$ID?> />
		<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>" />
		<?php
		$tabControl->Begin();
		$tabControl->BeginNextTab();
		?>

		<tr class="adm-detail-required-field">
			<td width="40%"><?=GetMessage("ESTELIFE_F_USER_ID")?></td>
			<td width="60%">
				<input type="hidden" name="user_type_id" value="3" />
				<input type="hidden" name="user_id" value="<?=$nUserId;?>" />
				<input type="text" name="user_name" size="30" data-input="user_id" value="<?=$sUserName;?>" />
			</td>
		</tr>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_COUNTRY")?></td>
			<td width="60%">
				<input type="hidden" name="country_id" size="60" maxlength="255" value="<?=$arResult['spec']['country_id']?>">
				<input type="text" name="country_name" data-input="country_id" size="60" maxlength="255" value="<?=$arResult['spec']['country']?>">
			</td>
		</tr>

		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_CITY")?></td>
			<td width="60%">
				<input type="hidden" name="city_id" size="60" maxlength="255" value="<?=$arResult['spec']['city_id']?>">
				<input type="text" name="city_name" data-input="city_id" size="60" maxlength="255" value="<?=$arResult['spec']['city']?>">
			</td>
		</tr>

		<tr>

			<td width="30%"><?=GetMessage('ESTELIFE_F_PHOTO')?></td>
			<td width="70%">
				<?echo CFileInput::Show("photo", $arResult['spec']['image_id'],
					array(
						"IMAGE" => "Y",
						"PATH" => "Y",
						"FILE_SIZE" => "Y",
						"DIMENSIONS" => "Y",
						"IMAGE_POPUP" => "Y",
						"MAX_SIZE" => array(
							"W" => 100,
							"H" => 100
						)
					), array(
						'upload' => true,
						'medialib' => true,
						'file_dialog' => true,
						'cloud' => true,
						'del' => true,
						'description' => false
					)
				);
				?>
			</td>
		</tr>

		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_SHORT_DESCRIPTION")?></td>
			<td width="60%">
				<input type="text" name="short_description" size="60" maxlength="255" value="<?=$arResult['spec']['short_description']?>">
			</td>
		</tr>
		<tr>
			<td width="40%"><?=GetMessage("ESTELIFE_F_FULL_DESCRIPTION")?></td>
			<td width="60%">
				<textarea cols="59" name="full_description" rows="10"><?=$arResult['spec']['full_description'];?></textarea>
			</td>
		</tr>
		<?php
		$tabControl->EndTab();
		$tabControl->Buttons(array("disabled"=>false, "back_url"=>(strlen($back_url) > 0 ? $back_url : "estelife_professionals_list.php?lang=".LANGUAGE_ID)));
		$tabControl->End();
		?>
	</form>
<?php
require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");