<?php
namespace lists\settings;

/**
 * Вероятно я забыл оставить описание файла. Обратитесь на мыло за уточнениями.
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 * @since 02.12.13
 */
class VDefault implements VSettings {
	public function countItems(){
		return 20;
	}

	public function viewType(){
		return self::CELLS;
	}

	public function sortField(){
		return 'name';
	}

	public function sortOrder(){
		return 'asc';
	}

	public function getPage(){
		$nPage=(isset($_GET['page'])) ?
			intval($_GET['page']) : 1;

		return ($nPage > 0) ?
			$nPage : 1;
	}
}