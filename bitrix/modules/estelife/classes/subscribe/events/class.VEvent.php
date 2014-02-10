<?php
namespace subscribe\events;
use subscribe\exceptions as errors;
use subscribe\owners\VOwner;

/**
 * Вероятно я забыл оставить описание файла. Обратитесь на мыло за уточнениями.
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 * @since 10.02.14
 */
class VEvent {
	protected $nType;
	protected $bTotal;
	protected $arFilter;
	protected $obOwner;
	protected $nElementId;

	public function __construct(VOwner $obOwner,$nType,$nElementId,$sFilter){
		$nType=intval($nType);

		if($nType<=0)
			throw new errors\VEventEx('undefined event type');

		$this->nType=$nType;
		$this->bTotal=(!empty($nElementId));
		$this->arFilter=(!empty($sFilter)) ? unserialize($sFilter) : array();
		$this->obOwner=$obOwner;
		$this->nElementId=intval($nElementId);
	}

	public function getType(){
		return $this->nType;
	}

	public function getTotal(){
		return $this->bTotal;
	}

	public function getFilter(){
		return $this->arFilter;
	}

	public function getOwner(){
		return $this->obOwner;
	}

	public function getElementId(){
		return $this->nElementId;
	}
}