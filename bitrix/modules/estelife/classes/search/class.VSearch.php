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

	public function __construct(){
		$this->obSph=new \SphinxClient();
		$this->obSph->SetServer('localhost', 3312);
		$this->obSph->SetMaxQueryTime(20);
		$this->obSph->SetArrayResult(true);
		$this->obSph->SetMatchMode(SPH_MATCH_ALL);
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
	}

	public function search($sQuery){
		if (empty($sQuery))
			return false;

		return $this->obSph->Query($sQuery.'*',$this->sIndex);
	}

	public function searchByTags($sTags){
		if (empty($sTags))
			return false;

		$this->obSph->SetMatchMode(SPH_MATCH_EXTENDED);
		return $this->obSph->Query('@search-tags: '.$sTags);
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
}