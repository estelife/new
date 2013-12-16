<?php
namespace lists\settings;

/**
 * Вероятно я забыл оставить описание файла. Обратитесь на мыло за уточнениями.
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 * @since 02.12.13
 */
interface VSettings {
	const TABLE=1;
	const CELLS=2;

	public function countItems();
	public function viewType();
	public function sortField();
	public function sortOrder();
	public function getPage();
}