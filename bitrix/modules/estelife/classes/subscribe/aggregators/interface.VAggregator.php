<?php
namespace subscribe\aggregators;

/**
 * Вероятно я забыл оставить описание файла. Обратитесь на мыло за уточнениями.
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 * @since 10.02.14
 */
interface VAggregator {
	public function aggregateItem($obItem);
}