<?php
namespace filters;

/**
 * Вероятно я забыл оставить описание файла. Обратитесь на мыло за уточнениями.
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 * @since 13.02.14
 */
interface VChangeable {
	public function __construct($sType);
	public function unsetParam($nKey);
	public function setParam($nKey,$sParam);
	public function clearParams();
}