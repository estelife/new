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
				$sContent = urlencode($sContent);

				$sYandexPwdKey = 'acd5afa742dbfeb79849459e0962e89470a4d7e8';
				$sYandexLogin = 'eugensereda7';
				$sYandexPass = '2131041';

				// Конфиг
				$login		 = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($sYandexPwdKey), base64_decode($sYandexLogin), MCRYPT_MODE_CBC, md5(md5($sYandexPwdKey))), "\0");
				$pwd		 = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($sYandexPwdKey), base64_decode($sYandexPass), MCRYPT_MODE_CBC, md5(md5($sYandexPwdKey))), "\0");
				$host_id	 = '62.109.11.71';
				$host_name	 = 'estelife.ru';

				// Ссылки
				$sUriLogin = "http://passport.yandex.ru/passport?mode=auth";
				$sUriRefferer = 'http://webmaster.yandex.ru/site/service-plugin.xml?host='.$host_id.'&service=ORIGINALS';

				if( empty($login) || empty($pwd) || empty($host_id) || empty($host_name) ){
					// Do nothing
				} else {
					# Формируем запрос
					$fields = '<wsw-fields><wsw-field name="host"><wsw-value>'.$host_name.' </wsw-value></wsw-field><wsw-field name="Original_text" ><wsw-value>'.$sContent.'</wsw-value></wsw-field></wsw-fields>';
					$request    = 'action=saveData&host='.$host_name.'&mvcDataLoadSignature=saveData&page=Originals-submission-form&service=ORIGINALS&wsw-fields='.$fields;

					#print $request;exit;

					#--------------------------------------------------#
					# Проходим авторизацию
					$ch = curl_init();                                        // инициализация UCP-cURL
					curl_setopt ($ch, CURLOPT_HEADER, 0);                     // получать заголовки
					curl_setopt ($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.3) Gecko/2008092417 Firefox/3.0.3');
					curl_setopt ($ch, CURLOPT_POST, 1);                       // использовать метод POST
					curl_setopt ($ch, CURLOPT_COOKIEJAR, 'cookie.txt');       // сохранять информацию Cookie в файл, чтобы потом можно было ее использовать
					curl_setopt ($ch, CURLOPT_COOKIEFILE, 'cookie_total.txt');
					curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);             // возвращать результат работы
					curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);             // не проверять SSL сертификат
					curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);             // не проверять Host SSL сертификата
					curl_setopt ($ch, CURLOPT_HTTPHEADER, array('Expect:'));  // это необходимо, чтобы cURL не высылал заголовок на ожидание
					//curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,15);            //
					curl_setopt($ch, CURLOPT_TIMEOUT,5);                      // Тайм Аут 5 секунд

					// Авторизируемся
					curl_setopt ($ch, CURLOPT_URL, $sUriLogin);
					curl_setopt ($ch, CURLOPT_REFERER, $sUriLogin);
					curl_setopt ($ch, CURLOPT_POSTFIELDS, 'login='.$login.'&passwd='.$pwd.'&twoweeks=yes');
					curl_exec   ($ch);
					$result = curl_multi_getcontent($ch);

					// Делаем запрос и парсим нужные данные
					curl_setopt ($ch, CURLOPT_URL, 'http://webmaster.yandex.ru/sites/');
					curl_setopt ($ch, CURLOPT_REFERER, $sUriRefferer);
					curl_setopt ($ch, CURLOPT_POST, 0);
					curl_setopt ($ch, CURLOPT_ENCODING,'gzip');
					curl_exec   ($ch);
					$result = curl_multi_getcontent($ch);

					// Парсим SK
					preg_match("/mvc\.obj\(\"user\"\,\[\[\"sk\"\,\"auth\"\]\,\[\"user\"\,\"([\w]+)\"\,true\,null\]\]\)\;/iU",$result, $ya_parse);
					$host_sk = $ya_parse[1];
					//print $ya_parse[1];
					//print $result;
					//exit;

					// Добавляем пост
					// http://webmaster.yandex.ru/site/plugins/wsw.api/api.xml?sk=uce0cdfb1fa8fb4beb9301c7e2a036b13
					curl_setopt ($ch, CURLOPT_URL, 'http://webmaster.yandex.ru/site/plugins/wsw.api/api.xml');
					curl_setopt ($ch, CURLOPT_REFERER, $sUriRefferer);
					curl_setopt ($ch, CURLOPT_POST, 1);
					curl_setopt ($ch, CURLOPT_ENCODING,'gzip');
					curl_setopt ($ch, CURLOPT_POSTFIELDS, $request.'&sk='.$host_sk.'');
					curl_exec   ($ch);

					$result = curl_multi_getcontent ($ch);


					if( preg_match( "#^mvc\.map\(.gate-errors#iU", $result ) ){
						$message = '<font color="red"><b>Ошибка :(</b></font><br /> Дамп: '.$result. 'yandex-content'.'<br />';
					}
					# SUCCESS
					elseif( preg_match( "#^mvc\.map\(.wsw_field_init#iU", $result ) ){
						$message =  'Статус: <font color="green"><b>OK</b></font><br />Статья добавлена в панель вебмастера.'. 'yandex-content' .'<br />';

						$obQuery = VDatabase::driver()->createQuery();
						$obQuery->builder()->from('estelife_yandex_content')
							->value('send', intval(1));

							$obQuery->builder()->filter()
								->_eq('iblock_element',$ID);
							$obQuery->update();
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

	$sSend = ($arRecord['send'] == 0) ? 'Нет' : 'Да';

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



//========= Групповое удаление, если права позволяют
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