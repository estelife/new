<?php
namespace subscribe\factories;
use subscribe\events\VEvent;
use subscribe\owners\VOwner;

/**
 * Вероятно я забыл оставить описание файла. Обратитесь на мыло за уточнениями.
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 * @since 10.02.14
 */
interface VFactory {
	public function __construct(VOwner $obOwner,VEvent $obEvent);
	public function getElements();
}