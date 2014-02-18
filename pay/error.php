<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule('estelife');


\notice\VNotice::registerError('Во время оплаты произошла ошибка!', 'Вероятно Вы указали неверные данные во время оформления платежа на стороне платежной системы. Попробуйте произвести оплату ещё раз.');
LocalRedirect('/education/');

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");