<?php
namespace core\database;

/**
 * Вероятно я забыл оставить описание. Пишите на почту, дам комментарий.
 * @since 03.10.13
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 */
class VListQueries implements \Iterator {
	protected $obDriver;
	private $arQueries;
	private $nCurrent;

	public function __construct(VDriver $obDriver){
		$this->obDriver=$obDriver;
		$this->arQueries=array();
	}

	public function createQuery(){
		$obQuery=$this->obDriver->createQuery();
		$this->arQueries[]=$obQuery;
		return $obQuery;
	}

	public function current(){
		return $this->valid() ?
			$this->arQueries[$this->nCurrent] :
			false;
	}

	public function next(){
		++$this->nCurrent;
	}

	public function key(){
		return $this->nCurrent;
	}

	public function valid(){
		return isset($this->arQueries[$this->nCurrent]);
	}

	public function rewind(){
		$this->nCurrent=0;
	}
}