<?php
namespace bitrix;
use core\types\VArray;

/**
 * Вероятно я забыл оставить описание файла. Обратитесь на мыло за уточнениями.
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 * @since 20.12.13
 */
class VNavigation {
	private $obResult;
	private $bAjax;

	public function __construct(\CAllDBResult $obResult,$bAjax=false){
		$this->obResult=$obResult;
		$this->bAjax=$bAjax;
	}

	public function getNav(){
		return ($this->bAjax) ?
			$this->getAjaxNav() :
			$this->obResult->GetNavPrint('', true,'text','/bitrix/templates/estelife/system/pagenav.php');
	}

	public function getAjaxNav(){
		$arDel=array("PAGEN_".$this->obResult->NavNum, "SIZEN_".$this->obResult->NavNum, "SHOWALL_".$this->obResult->NavNum, "PHPSESSID");
		$strNavQueryString = DeleteParam($arDel);
		$sUrlPath = GetPagePath();

		if($strNavQueryString <> "")
			$strNavQueryString = htmlspecialcharsbx("&".$strNavQueryString);

		$nPageWindow=$this->obResult->nPageWindow;

		if($this->obResult->NavPageNomer > floor($nPageWindow/2) + 1 && $this->obResult->NavPageCount > $nPageWindow)
			$nStartPage = $this->obResult->NavPageNomer - floor($nPageWindow/2);
		else
			$nStartPage = 1;

		if($this->obResult->NavPageNomer <= $this->obResult->NavPageCount - floor($nPageWindow/2) && $nStartPage + $nPageWindow-1 <= $this->obResult->NavPageCount)
			$nEndPage = $nStartPage + $nPageWindow - 1;
		else
		{
			$nEndPage = $this->obResult->NavPageCount;

			if($nEndPage - $nPageWindow + 1 >= 1)
				$nStartPage = $nEndPage - $nPageWindow + 1;
		}

		return array(
			'endPage'=>$nEndPage,
			'startPage'=>$nStartPage,
			'urlPath'=>$sUrlPath,
			'queryString'=>$strNavQueryString,
			'navNum'=>$this->obResult->NavNum,
			'pageNomer'=>$this->obResult->NavPageNomer,
			'pageCount'=>$this->obResult->NavPageCount
		);
	}
}