<?php
namespace subscribe\factories;
use subscribe\events\VEvent;

/**
 * Вероятно я забыл оставить описание файла. Обратитесь на мыло за уточнениями.
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 * @since 10.02.14
 */
interface VFactory {
	public function __construct(VEvent $obEvent);
	public function getTargetElements();
	public function getComplexElements();
}