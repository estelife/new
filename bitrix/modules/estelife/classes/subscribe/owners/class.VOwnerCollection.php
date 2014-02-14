<?php
namespace subscribe\owners;
use subscribe\events\VAggregator;
use subscribe\exceptions\VOwnerEx;

/**
 * Вероятно я забыл оставить описание файла. Обратитесь на мыло за уточнениями.
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 * @since 11.02.14
 */
class VOwnerCollection implements VOwnerEvents {
	/**
	 * @var VOwner[]
	 */
	protected $arOwners;

	public function __construct(array $arOwners){
		if(empty($arOwners))
			throw new VOwnerEx('not found owners for collection');

		foreach($arOwners as $arOwner){
			$obOwner=new VOwner(
				$arOwner['id'],
				$arOwner['email'],
				$arOwner['date_send']
			);

			$arTemp[]=$obOwner;
		}
	}

	public function getEvents(VAggregator $obAggregator=null){
		$arEvents=array();

		foreach($this->arOwners as $obOwner)
			$arEvents[]=$obOwner->getEvents($obAggregator);

		return $arEvents;
	}

	public function setEvent($nType, $nElementId, array $arFilter = null){
		foreach($this->arOwners as $obOwner)
			$obOwner->setEvent($nType,$nElementId,$arFilter);
	}
}