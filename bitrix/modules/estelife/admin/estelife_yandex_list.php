<script type="text/javascript" src="/bitrix/js/estelife/yandex.js"></script>
<?php
use core\database\mysql\VFilter;
use core\database\VDatabase;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

$sTableID = "tbl_estelife_yandex_content";
$oSort = new CAdminSorting($sTableID, "id", "asc");
$lAdmin = new CAdminList($sTableID, $oSort);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/estelife/prolog.php");

ClearVars();
$FORM_RIGHT = $APPLICATION->GetGroupRight("estelife");

if($FORM_RIGHT<="D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

CModule::IncludeModule("estelife");
IncludeModuleLangFile(__FILE__);



//===== FILTER ==========
$arFilterFields = Array(
	"find_id",
	"find_block",
	"find_name",
	"find_send",
);
$lAdmin->InitFilter($arFilterFields);

InitBVar($find_name_exact_match);
InitBVar($find_email_exact_match);
InitBVar($find_phone_exact_match);

$arFilter = Array(
	"id"						=> $find_id,
	"block"						=> $find_block,
	"name"						=> $find_name,
	"send"						=> $find_send,
);


//====== TABLE HEADERS =========
$headers = array(
	array("id"=>"ID", "content"=>GetMessage("ESTELIFE_F_ID"), "sort"=>"id", "default"=>true),
	array("id"=>"IBLOCK", "content"=>GetMessage("ESTELIFE_F_BLOCK"), "sort"=>"block_id", "default"=>true),
	array("id"=>"NAME", "content"=>GetMessage("ESTELIFE_F_NAME"), "sort"=>"name", "default"=>true),
	array("id"=>"SEND", "content"=>GetMessage("ESTELIFE_F_SEND"), "sort"=>"send", "default"=>true),
);

$lAdmin->AddHeaders($headers);


$obQuery= VDatabase::driver();

if(isset($_GET['code'])&& !empty($_GET['code'])){
	$nCode = $_GET['code'];

	$app_pass 	 =YA_APP_PASS;
	$app_id 	 =YA_APP_ID;


	$obQuery = $obQuery->createQuery();
	$obQuery->builder()
		->from('estelife_yandex');
	$obQuery->builder()
		->field('token');
	$obFilter=$obQuery->builder()->filter()->_eq('id',1);

	$obResult=$obQuery->select()->assoc();


	$url = 'https://oauth.yandex.ru/token';
	$postData = 'grant_type=authorization_code&code='.$code.'&client_id='.$app_id.'&client_secret='.$app_pass.'';
	$headers = array(
		'POST /token HTTP/1.1',
		'Host: oauth.yandex.ru',
		'Content-type: application/x-www-form-urlencoded',
		'Content-Length: ' . strlen($postData),
	);
	$curlOptions = array(
		CURLOPT_POST            => 1,
		CURLOPT_HEADER          => 0,
		CURLOPT_URL             => $url,
		CURLOPT_CONNECTTIMEOUT  => 1,
		CURLOPT_FRESH_CONNECT   => 1,
		CURLOPT_RETURNTRANSFER  => 1,
		CURLOPT_FORBID_REUSE    => 1,
		CURLOPT_TIMEOUT         => 5,
		CURLOPT_SSL_VERIFYPEER  => false,
		CURLOPT_POSTFIELDS      => $postData,
		CURLOPT_HTTPHEADER      => $headers
	);
	$ch = curl_init();
	curl_setopt_array($ch, $curlOptions);
	$result = curl_exec($ch);
	$info = curl_getinfo($ch);
    $result =json_decode($result);


	if($result->access_token){
		$token = $result->access_token;

		$obQuery->builder()
			->from('estelife_yandex');
		$obQuery->builder()
			->field('token');
		$obFilter=$obQuery->builder()->filter()->_eq('id',1);

		$obResult=$obQuery->select()->assoc();

		$obQuery->builder()->from('estelife_yandex')
			->value('id',1)
			->value('token', htmlspecialchars($token));
		$obQuery->builder()->filter()
			->_eq('id', 1);

		if(isset($obResult['token'])&& !empty($obResult['token'])){
			$obQuery->update();
		}else{
			$obQuery->insert();
		}

		echo "<h4>Код успешно получен</h4>";

	}else{
		$token = '';
		echo "<h4>Ошибка! Пожалуйста обновите страницу и повторите попытку</h4>";
	}




	die();
}


//==== Здесь надо зафигачить генерацию списка ========
if(($arID = $lAdmin->GroupAction()) && check_bitrix_sessid()){

	foreach($arID as $ID){
		if(($ID = IntVal($ID))>0 && $_REQUEST['action']=='send'){
			$obQuery=VDatabase::driver()->createQuery();
			$obQuery->builder()
				->from('iblock_element');
			$obQuery->builder()
				->field('DETAIL_TEXT');
			$obFilter=$obQuery->builder()->filter()->_eq('ID',$ID);

			$obResult=$obQuery->select()->assoc();
			$sContent = $obResult['DETAIL_TEXT'];



			$obQuery->builder()
				->from('estelife_yandex');
			$obQuery->builder()
				->field('token');
			$obFilter=$obQuery->builder()->filter()->_eq('id',1);

			$obResult=$obQuery->select()->assoc();

			$token = $obResult['token'];

			$code = $obResult['code'];
			$nSymbolCount = strlen(utf8_decode($sContent));
			$skipAddPost = false;
			$bLicense = false;

			if($nSymbolCount >= 500 && $nSymbolCount <= 32000 ){
				$bLicense = true;
			}else{
				$skipAddPost = true;
			}



			if($bLicense == true && $skipAddPost == false){


				// Кодируем текст статьи
				//$sContent = urlencode($sContent);

				//$sYandexPwdKey = 'acd5afa742dbfeb79849459e0962e89470a4d7e8';
				$sYandexLogin = 'maxim.shlemaryov';
				$sYandexPass = '120005konturi';

				// Конфиг
				$login		 = $sYandexLogin;
				$pwd		 = $sYandexPass;
				$host_id	 = '62.109.11.71';
				$host_name	 = YA_HOST_NAME;

				// Ссылки
				$sUriLogin = "http://passport.yandex.ru/passport?mode=auth";
				$sUriRefferer = 'http://webmaster.yandex.ru/site/service-plugin.xml?host='.$host_id.'&service=ORIGINALS';

				if( empty($login) || empty($pwd) || empty($host_id) || empty($host_name) ){
					// Do nothing
				} else {


					# Формируем запрос
					/*$fields = '<wsw-fields><wsw-field name="host"><wsw-value>'.$host_name.' </wsw-value></wsw-field><wsw-field name="Original_text" ><wsw-value>'.$sContent.'</wsw-value></wsw-field></wsw-fields>';
					$request    = 'action=saveData&host='.$host_name.'&mvcDataLoadSignature=saveData&page=Originals-submission-form&service=ORIGINALS&wsw-fields='.$fields;*/

					$xml = $sContent;
					$xml =urlencode('<original-text><content>'.$xml.'</content></original-text>');



					$headers = array(
						'Authorization: OAuth '.$token,
						'Content-Length: ' .strlen($xml)
					);

					// Добавляем пост
					// http://webmaster.yandex.ru/site/plugins/wsw.api/api.xml?sk=uce0cdfb1fa8fb4beb9301c7e2a036b13
					$ch = curl_init('http://webmaster.yandex.ru/api/v2/hosts/4324/original-texts/');
					curl_setopt ($ch, CURLOPT_URL, 'http://webmaster.yandex.ru/api/v2/hosts/4324/original-texts/');
					curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);
					curl_setopt ($ch, CURLOPT_POST, 1);
					curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt ($ch, CURLOPT_POSTFIELDS, $xml);

					$result = curl_exec($ch);
					$info = curl_getinfo($ch);


					$obQuery = VDatabase::driver()->createQuery();
					$obQuery->builder()->from('estelife_yandex_content')
						->value('send', intval(1));

					$obQuery->builder()->filter()
						->_eq('iblock_element',$ID);
					$obQuery->update();


					//$obQuery=\core\database\VDatabase::driver()->createQuery();
					if( preg_match( "#^mvc\.map\(.gate-errors#iU", $result ) ){
						$message = '<font color="red"><b>Ошибка :(</b></font><br /> Дамп: '.$result. 'yandex-content'.'<br />';
					}
					# SUCCESS
					elseif( preg_match( "#^mvc\.map\(.wsw_field_init#iU", $result )){
						$message =  'Статус: <font color="green"><b>OK</b></font><br />Статья добавлена в панель вебмастера.'. 'yandex-content' .'<br />';
					}
					# AUTHFAIL
					elseif( $result == 'AUTHFAIL' ){
						$message = '<font color="red"><b>Ошибка :</b></font><br /> Неправильная пара логин-пароль.'. 'yandex-content'.'<br />';
					}
					# OTHER ERRORS
					else {
						$message = '<font color="red"><b>Неизвестная ошибка :(</b></font><br /> Дамп:'.$result. 'yandex-content'.'<br />';
					}


					CEvent::Send("SEND_YANDEX_CONTENT_RESULT", "s1", array(
						'EMAIL_TO'=>'',
						'MESSAGE'=>$message,
					),"Y",62);

				}


			}

		}
	}
}


$obQuery=VDatabase::driver()->createQuery();
$obQuery->builder()->from('estelife_yandex_content','yc');
$obJoin=$obQuery->builder()->join();
$obJoin->_left()
	->_from('yc','iblock_element')
	->_to('iblock_element','ID','ct');
$obQuery->builder()
	->field('yc.iblock_element','id')
	->field('ct.IBLOCK_ID')
	->field('ct.NAME','name')
	->field('yc.send','send');
$obFilter=$obQuery->builder()->filter();

if($_GET && $_GET['set_filter'] == 'Y'){
	if(!empty($arFilter['id']))
		$obFilter->_eq('yc.iblock_element',$arFilter['id']);
	if(!empty($arFilter['block']))
		$obFilter->_eq('ct.IBLOCK_ID',$arFilter['block']);
	if(!empty($arFilter['name']))
		$obFilter->_like('ct.NAME',$arFilter['name'],VFilter::LIKE_BEFORE|VFilter::LIKE_AFTER);
	if(!empty($arFilter['send'])){
		if($arFilter['send']== 'yes'){
			$obFilter->_eq('yc.send',1);
		}else if($arFilter['send']=='accept'){
			$obResult->_eq('yc.send',2);
		}else{
			$obFilter->_eq('yc.send',0);
		}
	}

}


if($by=='id')
	$obQuery->builder()->sort('yc.iblock_element',$order);
elseif($by=='block_id')
	$obQuery->builder()->sort('ct.IBLOCK_ID',$order);
elseif($by=='name')
	$obQuery->builder()->sort('ct.NAME',$order);
elseif($by=='send')
	$obQuery->builder()->sort('yc.send',$order);
else
	$obQuery->builder()->sort($by,$order);


$obResult=$obQuery->select();
$obResult=new CAdminResult(
	$obResult->bxResult(),
	$sTableID
);



$obResult->NavStart();
$lAdmin->NavText($obResult->GetNavPrint(GetMessage('ESTELIFE_PAGES')));


while($arRecord=$obResult->Fetch()){

	$f_ID=$arRecord['id'];
	$row =& $lAdmin->AddRow($f_ID,$arRecord);

	$sSend = '';

	if($arRecord['send']==1){
		$sSend = 'Отправлено';
	}else if($arRecord['send'] ==2){
		$sSend = 'Подтверждено';
	}else{
		$sSend = 'В ожидании';
	}

	$row->AddViewField("ID",$arRecord['id']);
	$row->AddViewField("IBLOCK", $arRecord['IBLOCK_ID']);
	$row->AddViewField("NAME", $arRecord['name']);
	$row->AddViewField("SEND", $sSend);

	$arActions = Array();
	$arActions[] = array("TITLE"=>GetMessage("ESTELIFE_ACTION_SEND"),"ACTION"=>"javascript:if(confirm('".GetMessage("ESTELIFE_CONFIRM_SEND")."')) window.location='?lang=".LANGUAGE_ID."&action=send&ID=$f_ID&".bitrix_sessid_get()."'","TEXT"=>GetMessage("ESTELIFE_ACTION_SEND"));
	$row->AddActions($arActions);

}



$lAdmin->AddFooter(
	array(
		array("title"=>GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>1),//$rsData->SelectedRowsCount()),
		array("counter"=>true, "title"=>GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value"=>"0"),
	)
);



//if ($FORM_RIGHT=="W")
$lAdmin->AddGroupActionTable(Array(
	"send"=>GetMessage("FORM_SEND_L"),
));




//======= Контектстное меню ===========
//if ($FORM_RIGHT=="W")
//{
$aMenu = array();
$aMenu[] = array(
	"TEXT"	=>GetMessage("ESTELIFE_CREATE"),
	"TITLE"=>GetMessage("ESTELIFE_CREATE_TITLE"),
	"LINK"=>"estelife_subscribe_edit.php?lang=".LANG,
	"ICON" => "btn_new"
);



$lAdmin->CheckListMode();

$APPLICATION->SetTitle(GetMessage("ESTELIFE_HEAD_TITLE"));



require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

?>
	<script type="text/javascript" src="/bitrix/js/estelife/jquery-1.10.2.min.js"></script>
	<script type="text/javascript" src="/bitrix/js/estelife/jquery-ui-1.10.3.custom.min.js"></script>
	<script type="text/javascript" src="/bitrix/js/estelife/estelife.js"></script>

	<a name="tb"></a>

	<form name="form1" method="GET" action="<?=$APPLICATION->GetCurPage()?>?">
		<?php
		$oFilter = new CAdminFilter(
			$sTableID."_filter",
			array(
				GetMessage("ESTELIFE_F_ID"),
				GetMessage("ESTELIFE_F_BLOCK"),
				GetMessage("ESTELIFE_F_NAME"),
				GetMessage("ESTELIFE_F_SEND"),
			)
		);
		$oFilter->Begin();
		?>
		<tr>
			<td><?echo GetMessage("ESTELIFE_F_ID")?></td>
			<td><input type="text" name="find_id" size="30" value="<?echo htmlspecialcharsbx($find_id)?>"></td>
		</tr>

		<tr>
			<td><?echo GetMessage("ESTELIFE_F_BLOCK")?></td>
			<td><input type="text" name="find_block" size="30" value="<?echo htmlspecialcharsbx($find_block)?>"></td>
		</tr>
		<tr>
			<td><?echo GetMessage("ESTELIFE_F_NAME")?></td>
			<td><input type="text" name="find_name" size="30" value="<?echo htmlspecialcharsbx($find_name)?>"></td>
		</tr>

		<tr>
			<td><?echo GetMessage("ESTELIFE_F_SEND")?></td>
			<td>
				<select name="find_send" value="<?echo htmlspecialcharsbx($find_send)?>">
					<option value="0"><?echo GetMessage("ESTELIFE_NOT_IMPORTANT")?></option>
					<option value="accept">Подтверждено</option>
					<option value="yes">Да</option>
					<option value="no">Нет</option>
				</select>
			</td>
		</tr>

		<?
		$oFilter->Buttons(array("table_id"=>$sTableID, "url"=>$APPLICATION->GetCurPage()));
		$oFilter->End();
		#############################################################
		?>
	</form>

<?

$lAdmin->DisplayList();

print "<form method='post' action='/'>\n";
print "<div class='wrap'>";

$popupUrl = 'https://oauth.yandex.ru/authorize?response_type=code&client_id=11156d008f04494596948f23d5f30787&display=popup';
$popupTitle = "Yandex Code";
print "<p><a href='#' onclick='popup(\"$popupUrl\", \"$popupTitle\");'>Получить код подтверждения</a>";

print "</div>";
print '</form></div>';