<?php
namespace core\database\collections;
use core\utils\types\VArray;

/**
 * Описание класса не задано. Обратитесь с вопросом на почту разработчика.
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 * @since 03.10.13
 */
class VMeta {
	const MANY_TO_ONE=1;
	const ONE_TO_ONE=2;
	const ONE_TO_MANY=3;

	protected $arMeta;
	protected $obCollection;
	protected $arTypes;
	protected $sPrimary;
	protected $arJoined;

	/**
	 * Фомирует масив с метаданными на основе переданной информации и набора правил
	 * @param VCollection $obCollection
	 * @param array $arMeta
	 */
	public function __construct(VCollection $obCollection,array $arMeta){
		$this->obCollection=$obCollection;
		$this->arTypes=array(
			'string',
			'int',
			'boolean'
		);
		$this->sPrimary=false;
		$this->arJoined=array();
		$this->arMeta=array();

		if(!empty($arMeta)){
			foreach($arMeta as $sField=>$arValue){

				$this->arMeta[$sField]['type']=(isset($arValue['type']) &&
					in_array($arValue['type'],$this->arTypes)) ?
					$arValue['type'] : 'string';

				$this->arMeta[$sField]['length']=(isset($arValue['length'])) ?
					intval($arValue['length']) : 0;

				if(isset($arValue['primary'])){
					$this->arMeta[$sField]['primary']=true;
					$this->sPrimary=$sField;
				}

				if(empty($arValue['join']))
					continue;

				foreach($arValue['join'] as $sCollection=>$arJoin){
					$this->arMeta[$sField]['join'][$sCollection]=array(
						'field'=>isset($arJoin['field']) ?
							$arJoin['field'] : false,
						'type'=>(isset($arJoin['type'])) ?
							intval($arJoin['type']) : false
					);
				}

				$this->arJoined[]=$sField;
			}
		}

		$arFields=$this->obCollection
					->driver()
					->createSpecialQuery()
					->getFields($this->obCollection->name());

		if(!empty($arFields)){
			foreach($arFields as $sField=>$arData)
				if(!isset($this->arMeta[$sField]))
					$this->arMeta[$sField]=$arData;
		}

		if(!empty($this->arJoined))
			$this->arJoined=array_flip($this->arJoined);
	}

	/**
	 * Вовзращает даные поля по его названию
	 * @param $sField
	 * @return array
	 */
	public function field($sField){
		return (isset($this->arMeta[$sField])) ?
			$this->arMeta[$sField] :
			array();
	}

	/**
	 * Возвращает данные поля, если оно ялвяется объединящим две таблицы / колекции
	 * @param $sField
	 * @return array|bool
	 */
	public function joined($sField){
		return (isset($this->arJoined[$sField]) &&
			$this->validJoin($this->arMeta[$sField])) ?
			$this->arMeta[$sField]['join'] :
			false;
	}

	/**
	 * Возвращает массив полей для связи данныз или false
	 * @return array|bool
	 */
	public function joinedFields(){
		if(!empty($this->arJoined)){
			foreach($this->arJoined as $sField=>&$mValue){
				if($this->validJoin($this->arMeta[$sField]))
					$mValue=$this->arMeta[$sField]['join'];
				else
					unset($this->arJoined[$sField]);
			}

			return (!empty($this->arJoined)) ?
				$this->arJoined :
				false;
		}

		return false;
	}

	/**
	 * Возвращает название поля, которое является первичным ключом
	 * @return string|bool
	 */
	public function primary(){
		return $this->sPrimary;
	}

	/**
	 * Возвращает список полей для коллекции или false, если поля не были описаны
	 * @return array|bool
	 */
	public function fields(){
		return (!empty($this->arMeta)) ?
			$this->arMeta :
			false;
	}

	/**
	 * Осуществляет валидацию join на этапе связывания. Причина поздней валидации кроется в
	 * готовности всех объектов к связыванию на этот момент
	 * @param array $arField
	 * @return bool
	 */
	protected function validJoin(array &$arField){
		if(isset($arField['join_validated']))
			return $arField['join_validated'];

		$arField['join_validated']=false;

		if(empty($arField['join']))
			return false;

		foreach($arField['join'] as $sCollection=>&$arJoin){
			if(empty($sCollection) ||
				!VManager::has($sCollection)){
				unset($arField['join'][$sCollection]);
				continue;
			}

			$obCollection=VManager::get($sCollection);
			$nType=$arJoin['type'];
			$sField=$arJoin['field'];

			if($sField && ($arJoined=$obCollection->meta()->joined($sField)) &&
				isset($arJoined[$this->obCollection->name()])){

				if(!$nType){
					$nJoinedType=$arJoined[$this->obCollection->name()]['type'];

					if($nJoinedType==self::MANY_TO_ONE)
						$nType=self::ONE_TO_MANY;
					else if($nJoinedType==self::ONE_TO_MANY)
						$nType=self::MANY_TO_ONE;
					else
						$nType=self::ONE_TO_ONE;
				}

			}else if($obCollection->meta()->primary())
				$sField=$obCollection->meta()->primary();

			if(!$sField){
				unset($arField['join'][$sCollection]);
				continue;
			}

			$arJoin['field']=$sField;
			$arJoin['type']=($nType) ?
				$nType : VMeta::ONE_TO_ONE;
		}

		if(empty($arField['join']))
			return false;

		$arField['join_validated']=true;
		return true;
	}
}