<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
/**
 * Вероятно я забыл оставить описание файла. Обратитесь на мыло за уточнениями.
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 * @since 05.12.13
 */
$obClinic=new \orm\items\VClinic();
\core\types\VArray::prePrint($obClinic);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");