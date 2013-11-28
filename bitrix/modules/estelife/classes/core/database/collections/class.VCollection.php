<?php
namespace core\database\collections;
use core\database\exceptions\VCollectionException;
use core\database\VDatabase;
use core\database\VDriver;
use core\database\VQuery;
use core\database\VResult;

/**
 * Вероятно я забыл оставить описание. Пишите на почту, дам комментарий.
 * @since 03.10.13
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 */
class VCollection {
	/**
	 * Драйвер базы данных, с которым общается коллекция
	 * @var VDriver
	 */
	protected $obDriver;

	/**
	 * Название коллекции, с которой работате объект
	 * @var string
	 */
	private $sCollection;

	/**
	 * Мета информация по полям коллекции. На данный момент используется для
	 * формирования связей. Возможно стоит расширить применение.
	 * @var VMeta
	 */
	private $obMeta;

	/**
	 * Промежуточный вариант запроса
	 * @var VQuery
	 */
	private $obQuery;

	/**
	 * Задает название коллекции
	 * @param $sCollection
	 * @param array $arMeta
	 * @internal param \core\database\VDriver $obDriver
	 */
	public function __construct($sCollection,array $arMeta=null){
		$this->sCollection=$sCollection;
		$this->obDriver=VDatabase::driver();

		if(!empty($arMeta))
			$this->obMeta=new VMeta($this,$arMeta);

		VManager::register($this);
	}

	/**
	 * Создает промежуточный вариант запроса
	 * @return \core\database\VQuery
	 */
	public function createQuery(){
		$this->obQuery=$this->obDriver->createQuery();
		return $this->obQuery;
	}

	/**
	 * Возвращает прямой список записей без дополнительных связей
	 * @return VListRecords
	 */
	public function lineList(){
		$obQuery=(!$this->obQuery) ?
			$this->obDriver->createQuery() :
			$this->obQuery;

		$obBuilder=$obQuery->builder();
		$obBuilder->from($this->sCollection);

		$obResult=$obQuery->select();
		$obList=new VListRecords();

		if($obResult->count()>0){
			while($arRecord=$obResult->assoc())
				$obList->set(new VRecord($this,$arRecord));
		}

		return $obList;
	}

	/**
	 * Получает список записей для данной коллекции
	 * return VListRecords
	 * @todo подвергнуть жесткой оптимизации. Оптимизировать код, предусмотреть более глубокие связи (коллекция-коллекция-коллекция...)
	 */
	public function	fullList(){
		if($arJoined=$this->meta()->joinedFields()){
			$obQuery=(!$this->obQuery) ?
				$this->obDriver->createQuery() :
				$this->obQuery;

			$obBuilder=$obQuery->builder();
			$obBuilder->from($this->sCollection);

			$obCollection=null;
			$arFields=null;
			$arOneToMany=null;
			$arJoinResult=null;

			foreach($arJoined as $sField=>$arJoin){
				foreach($arJoin as $sCollection=>$arData){
					if(!VManager::has($sCollection))
						continue;
					else if($arData['type']==VMeta::ONE_TO_MANY){
						if(!is_array($arOneToMany))
							$arOneToMany=array();

						$arOneToMany[$sField]=array(
							'collection'=>$sCollection,
							'field'=>$arData['field']
						);
						continue;
					}

					$obCollection=VManager::get($sCollection);
					$arFields=$obCollection->meta()->fields();

					if(!$arFields)
						continue;

					$obBuilder->join()->_left()
						->_from($this->name(),$sField)
						->_to($sCollection,$arData['field']);

					foreach($arFields as $sField)
						$obBuilder->field(
							$sCollection.'.'.$sField,
							$sCollection.'_'.$sField
						);

					$arJoinResult[]=$sCollection;
				}
			}

			foreach($this->meta()->fields() as $sField=>$arData)
				$obBuilder->field(
					$this->name().'.'.$sField,
					$this->name().'_'.$sField
				);

			unset($obCollection,$arFields);

			$obResult=$obQuery->select();
			$obList=new VListRecords();

			if($obResult->count()>0){
				$bOneToMany=!empty($arOneToMany);
				$bJoined=!empty($arJoinResult);
				$arAssociated=array();
				$arOtherCollections=null;
				$arMatches=null;
				$arRecords=array();

				while($arRecord=$obResult->assoc()){
					if($bJoined){
						$arOtherCollections=array();

						if(!empty($arAssociated)){
							foreach($arAssociated as $sField=>$arData)
								$arOtherCollections[$arData['collection']][$arData['field']]=$arRecord[$sField];
						}else{
							foreach($arJoinResult as $sCollection){
								foreach($arRecord as $sField=>$mValue){
									if(preg_match('#^'.$sCollection.'_(.*)$#',$sField,$arMatches)){
										$arOtherCollections[$sCollection][$arMatches[1]]=$mValue;
										$arAssociated[$sField]=array(
											'collection'=>$sCollection,
											'field'=>$arMatches[1]
										);
										unset($arRecord[$sField]);
									}
								}
							}
						}

						if(!empty($arOtherCollections)){
							foreach($arOtherCollections as $sCollection=>$arData){
								$arRecord[$sCollection]=new VRecord(
									VManager::get($sCollection),
									$arData
								);
							}
						}
					}

					if(!empty($arRecord)){
						foreach($arRecord as $sField=>$mValue){
							$arRecord[str_replace($this->name().'_','',$sField)]=$mValue;
							unset($arRecord[$sField]);
						}
					}

					if($bOneToMany){
						foreach($arOneToMany as $sField=>&$arJoin){
							$arJoin['values'][]=$arRecord[$sField];
							$arRecords[]=$arRecord;
						}
					}else
						$obList->set(new VRecord($this,$arRecord));
				}

				unset(
					$bJoined,
					$arJoinResult,
					$arAssociated,
					$arOtherCollections,
					$arMatches
				);

				if($bOneToMany){
					$nCurrentRecord=0;

					foreach($arOneToMany as $sField=>$arJoin){
						$obCollection=VManager::get($arJoin['collection']);
						$obQuery=$obCollection->createQuery();
						$obQuery->builder()
							->filter()
							->_in($arJoin['field'],$arJoin['values']);

						$obRecords=$obCollection->lineList();
						$arJoin['values']=array_flip($arJoin['values']);

						foreach($obRecords as $obRecord){
							if(!isset($obRecord[$arJoin['field']],$arJoin['values'][$obRecord[$arJoin['field']]]))
								continue;

							$nCurrentRecord=$arJoin['values'][$obRecord[$arJoin['field']]];
							$arRecords[$nCurrentRecord][$arJoin['collection']][]=$obRecord;
						}
					}

					foreach($arRecords as $arRecord)
						$obList->set(new VRecord($this,$arRecord));
				}
			}

			unset(
				$bOneToMany,
				$arOneToMany,
				$arJoined,
				$obBuilder,
				$arJoinResult,
				$arRecords
			);

			return $obList;
		}else{
			return $this->lineList();
		}
	}

	/**
	 * Получает одну запись из коллекции
	 * @param bool $mPrimaryValue
	 * @throws \core\database\exceptions\VCollectionException
	 * @return VRecord
	 */
	public function record($mPrimaryValue=false){
		$obList=null;

		if($mPrimaryValue && $sPrimary=$this->obMeta->primary()){
			$obQuery=$this->createQuery();
			$obQuery->builder()->filter()->_eq($sPrimary,$mPrimaryValue);
			$obQuery->builder()->slice(0,1);
			$obList=$this->lineList();
		}

		if(!$obList || count($obList)==0)
			throw new VCollectionException('record not found');

		return $obList->current();
	}

	/**
	 * Создвет пустую запись
	 * @return VRecord
	 */
	public function create(){
		return new VRecord($this,array());
	}

	/**
	 * Очишает коллекцию от записей
	 * @param VQuery $obQuery
	 * @return VResult
	 */
	public function clear(){
		$obQuery=(!$this->obQuery) ?
			$this->driver()->createQuery() :
			$this->obQuery;
		$obQuery->builder()->from($this->name());
		return $obQuery->delete();
	}

	/**
	 * Осущетслвяет перемещеие записи в другую коллекию
	 * @param VRules $obRules
	 * @return VRecord
	 */
	public function move(VRules $obRules){
		$obRecord=$obRules->record();
		$obNew=$obRules->prepare($this);
		$this->delete($obRecord);
		return $obNew;
	}

	/**
	 * Копирует запись в другую коллекцию
	 * @param VRules $obRules
	 * @return VRecord
	 */
	public function copy(VRules $obRules){
		return $obRules->prepare($this);
	}

	/**
	 * Добавляет запись в коллекцию или обновляет её
	 * @param VRecord $obRecord
	 * @return \core\database\collections\VRecord
	 */
	public function write(VRecord &$obRecord){
		if(!$obRecord->isChanged())
			return $obRecord;

		$obQuery=$this->obDriver->createQuery();
		$obQuery->builder()->from($this->sCollection);

		foreach($obRecord as $sKey=>$sValue){
			$obQuery->builder()->value($sKey,$sValue);
		}

		if(($sField=$this->meta()->primary()) &&
			isset($obRecord[$sField])){

			$obQuery->builder()->filter()
				->_eq(
					$sField,
					$obRecord[$sField]
				);
			$obQuery->update();
		}else{
			$obResult=$obQuery->insert();
			$obRecord[$this->meta()->primary()]=$obResult->insertId();
		}

		return $obRecord;
	}

	/**
	 * Удаляет запись из коллекции
	 * @param VRecord $obRecord
	 * @throws \core\database\exceptions\VCollectionException
	 * @return VResult
	 */
	public function delete(VRecord $obRecord){
		$obQuery=$this->obDriver->createQuery();

		if(!($sField=$this->meta()->primary()) &&
			!isset($obRecord[$sField]))
			throw new VCollectionException('record not be delete: not found identifier');

		$obQuery->builder()
			->from($this->sCollection)
			->filter()
			->_eq($sField,$obRecord[$sField]);

		return $obQuery->delete();
	}

	/**
	 * Возвращает мета информацию
	 * @return VMeta
	 */
	public function meta(){
		return $this->obMeta;
	}

	/**
	 * Возвращает драйвер базы, с которым работает коллекцию
	 * @return VDriver|\core\mysql\VMysql
	 */
	public function driver(){
		return $this->obDriver;
	}

	/**
	 * Возвращает название колллекции
	 * @return string
	 */
	public function name(){
		return $this->sCollection;
	}
}