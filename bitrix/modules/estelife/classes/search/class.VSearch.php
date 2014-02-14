<?php
namespace search;
/**
 *
 * @author Panait Vitaly <panait.v@yandex.ru>
 * @since 06.02.14
 */
class VSearch{
	protected $sIndex;
	protected $arParams;
	private $obSph;
	private $arResult;
	protected $arRelevant;

	public function __construct(){
		$this->obSph=new \SphinxClient();
		$this->obSph->SetServer('localhost', 3312);
		$this->obSph->SetMaxQueryTime(20);
		$this->obSph->SetArrayResult(true);
		$this->obSph->SetMatchMode(SPH_MATCH_ANY);
		$this->obSph->SetLimits(0,1000);
		$this->obSph->SetFieldWeights(array(
			'search-name'=>100,
			'search-category'=>80,
			'search-preview'=>60,
			'search-detail'=>70,
			'search-tags'=>90
		));
		$this->obSph->ResetFilters();
		$this->sIndex='estelife';
		$this->arRelevant=array(21, 20, 8, 9, 6, 7, 5, 4, 12, 13, 15, 1, 2, 3, 4, 10, 11);
	}

	public function search($sQuery, $sField=false){
		if (empty($sQuery))
			return array();

		if (!$sField)
			$arResult=$this->obSph->Query($sQuery.'*',$this->sIndex);
		else{
			$this->obSph->SetMatchMode(SPH_MATCH_EXTENDED);
			$arResult=$this->obSph->Query('@search-'.$sField.': '.$sQuery);
		}

		if (!empty($arResult['matches'])){
			$arResult=$arResult['matches'];
			foreach ($arResult as $val){
				$val=$val['attrs'];
				$this->arResult[]=$val;
			}
			unset($arResult);
			return $this->arResult;
		}else
			return array();
	}

	public function getRelevantResult(){
		if (empty($this->arResult))
			return false;

		$arResult=array();
		if (!empty($this->arRelevant)){
			foreach ($this->arRelevant as $val){
				if (!empty($this->arResult[$val]))
					$arResult=array_merge($arResult, $this->arResult[$val]);
			}
		}

		return $arResult;
	}

	public function setFilter($sAttr, array $arValues){
		if (empty($sAttr) || empty($arValues))
			return false;

		$this->obSph->setFilter($sAttr, $arValues);
	}

	public function setIndex($sIndex){
		if (empty($sIndex))
			return false;

		$this->sIndex=$sIndex;
	}

	public function setSort($sSort){
		if (empty($sSort))
			return false;

		$this->obSph->SetSortMode(SPH_SORT_ATTR_DESC, $sSort);
	}

	public function setRelevantArray(array $arRelevant){
		$this->arRelevant=$arRelevant;
	}
}