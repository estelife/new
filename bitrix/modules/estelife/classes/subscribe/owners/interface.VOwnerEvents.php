<?php
namespace subscribe\owners;
use subscribe\events\VAggregator;

/**
 * Вероятно я забыл оставить описание файла. Обратитесь на мыло за уточнениями.
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 * @since 11.02.14
 */
interface VOwnerEvents {
	public function getEvents(VAggregator $obAggregator=null);
	public function setEvent($nType,$nElementId,array $arFilter=null);
}