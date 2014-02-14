<?php
namespace subscribe\events;
use subscribe\owners\VOwner;

/**
 * Вероятно я забыл оставить описание файла. Обратитесь на мыло за уточнениями.
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 * @since 11.02.14
 */
interface VAggregator {
	public function checkEvent(VEvent $obEvent);
	public function aggregateEvent(VOwner $obOwner, VEvent $obEvent);
}