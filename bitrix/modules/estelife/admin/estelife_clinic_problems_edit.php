<?php
use core\database\exceptions\VCollectionException;
use core\database\VDatabase;
use core\types\VArray;
use core\exceptions as ex;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/estelife/prolog.php");

ClearVars();
$FORM_RIGHT = $APPLICATION->GetGroupRight("estelife");

if($FORM_RIGHT<="D")
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

CModule::IncludeModule("estelife");
IncludeModuleLangFile(__FILE__);

define("HELP_FILE","estelife_list.php");
$ID = isset($_REQUEST['ID']) ?
    intval($_REQUEST['ID']) : 0;

$obRecord=null;
$obColl= VDatabase::driver();

if(!empty($ID)){
    try{
        $obQuery = $obColl->createQuery();
        $obQuery->builder()->from('estelife_clinic_problems')
            ->filter()
            ->_eq('id', $ID);
        $arResult = $obQuery->select()->assoc();
    }catch(VCollectionException $e){}
}

if($_SERVER['REQUEST_METHOD']=='POST'){
    $obPost=new VArray($_POST);
    $obError=new ex\VFormException();

    try{
        if($obPost->blank('title'))
            $obError->setFieldError('TITLE_IS_EMPTY','title');

        $obError->raise();

        $obQuery = $obColl->createQuery();
        $obQuery->builder()->from('estelife_clinic_problems')
            ->value('title', trim(strip_tags($obPost->one('title'))));

        if (!empty($ID)){
            $obQuery->builder()->filter()
                ->_eq('id',$ID);
            $obQuery->update();
            $idMethod = $ID;
        }else{
            $idMethod = $obQuery->insert()->insertId();
            $ID =$idMethod;

        }

        if(!empty($idMethod)){
            if(!$obPost->blank('save'))
                LocalRedirect('/bitrix/admin/estelife_clinic_problems_list.php?lang='.LANGUAGE_ID);
            else
                LocalRedirect('/bitrix/admin/estelife_clinic_problems_edit.php?lang='.LANGUAGE_ID.'&ID='.$idMethod);
        }
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
    array("DIV" => "edit1", "TAB" => GetMessage("ESTELIFE_T_BASE"), "ICON" => "estelife_r_base", "TITLE" => GetMessage("ESTELIFE_T_BASE_TITLE"))
);
$tabControl = new CAdminTabControl("estelife_clinic_problems_".$ID, $aTabs, true, true);
$message = null;

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
            $arResult[$sKey]=$sValue;
    }
}
?>
    <form name="estelife_spec" method="POST" action="<?echo $APPLICATION->GetCurPage()?>" enctype="multipart/form-data">
        <?=bitrix_sessid_post()?>
        <input type="hidden" name="ID" value=<?=$ID?> />
        <input type="hidden" name="lang" value="<?=LANGUAGE_ID?>" />
        <?php
        $tabControl->Begin();
        $tabControl->BeginNextTab()
        ?>

        <tr class="adm-detail-required-field">
            <td width="40%"><?=GetMessage("ESTELIFE_F_TITLE")?></td>
            <td width="60%"><input type="text" name="title" size="60" maxlength="255" value="<?=$arResult['title']?>"></td>
        </tr>

        <?php
        $tabControl->EndTab();
        $tabControl->Buttons(array("disabled"=>false, "back_url"=>(strlen($back_url) > 0 ? $back_url : "estelife_clinic_problems_list.php?lang=".LANGUAGE_ID)));
        $tabControl->End();
        ?>
    </form>
<?php
require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");