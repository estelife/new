<?php
namespace lists;
use core\database\VDatabase;
use lists\adapters\VAdapter;
use lists\settings\VSettings;

/**
 * Вероятно я забыл оставить описание файла. Обратитесь на мыло за уточнениями.
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 * @since 02.12.13
 */
abstract class VList {
	/**
	 * @var \lists\VFilter $obFilter
	 */
	protected $obFilter;
	/**
	 * @var VSettings $obType
	 */
	protected $obSettings;
	/**
	 * @var int $nCount
	 */
	protected $nCount;
	/**
	 * @var array
	 */
	protected $arPages;

	public function __construct(){
		$this->nCount=1;
	}

	/**
	 * Создает объект фильтр для данного списка
	 * @return \lists\VFilter
	 */
	public function createFilter(){
		if(!$this->obFilter)
			$this->obFilter=new VFilter($this);

		return $this->obFilter;
	}

	/**
	 * Задает тип отображения списка и его настройки
	 * @param settings\VSettings $obSettings
	 */
	public function setSettings(VSettings $obSettings){
		$this->obSettings=$obSettings;
	}

	/**
	 * Всегда возращает false или массив из двух элементов array('id','asc')
	 * @return bool|array
	 */
	protected function getSort(){
		if($this->obSettings){
			return array(
				$this->obSettings->sortField(),
				$this->obSettings->sortOrder()
			);
		}

		return false;
	}

	/**
	 * Всегда возвращает false или массив из 2-х элементов array(0,10)
	 * @return bool|array
	 */
	protected function getLimit(){
		if($this->obSettings){
			$nPage=$this->obSettings->getPage();
			$nOffset=$this->obSettings->countItems();

			return array(
				($nPage-1)*$nOffset,
				$nOffset
			);
		}

		return false;
	}

	/**
	 * Задает кол-во найденных элементов. Обязательно использовать в методе items,
	 * чтобы корректно генерировались данные для постранички
	 * @param $nCount
	 */
	protected function setFindCount($nCount){
		$this->nCount=abs(intval($nCount));
	}

	/**
	 * Вовзращает данные для генерации шаблона постранично навигации.
	 * @return array
	 */
	protected function pagenavData(){
		if(!$this->arPages){
			$nPageSize=($this->obSettings) ?
				$this->obSettings->countItems() : 10;
			$nPageCount=floor($this->nCount/$nPageSize);

			if($nPageCount%$nPageSize > 0)
				$nPageCount++;

			$nCurrentPage=($this->obSettings) ?
				$this->obSettings->getPage() : 1;

			if($nCurrentPage+floor($nPageSize/2)>=$nPageCount)
				$nEndPage=$nPageCount;
			else{
				if($nCurrentPage+floor($nPageSize/2)>=$nPageSize)
					$nEndPage=$nCurrentPage+floor($nPageSize/2);
				else{
					if($nPageCount>=$nPageSize)
						$nEndPage=$nPageSize;
					else
						$nEndPage=$nPageCount;
				}
			}

			$nStartPage=($nEndPage-$nPageSize >= 0) ?
				$nEndPage - $nPageSize + 1 : 1;

			$this->arPages=array(
				'pages'=>range($nStartPage,$nEndPage),
				'prev'=>($nCurrentPage>1) ?
					$nCurrentPage-1 : false,
				'next'=>($nCurrentPage<$nPageCount) ?
					$nCurrentPage+1 : false
			);
		}

		return $this->arPages;
	}

	/**
	 * Этот метод нао переопределить для получения списка элемнетов
	 * @param adapters\VAdapter $obAdapter
	 * @return array
	 */
	abstract public function getItems(VAdapter $obAdapter);

	/**
	 * Этот метод требуется переопределить для получения одного элемента
	 * @param $mIdent
	 * @return mixed
	 */
	abstract public function getItem($mIdent);
}