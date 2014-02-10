<?php
namespace subscribe\aggregators;

/**
 * Описание класса не задано. Обратитесь с вопросом на почту разработчика.
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 * @since 07.02.14
 */
class VEvent implements VAggregator {
	public function nextItem($obItem){
		if(!is_object($obItem) || !($obItem instanceof \subscribe\VEvent))
			return false;
	}
}