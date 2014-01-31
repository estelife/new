<?php
namespace bitrix;

/**
 * Вероятно я забыл оставить описание файла. Обратитесь на мыло за уточнениями.
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 * @since 20.12.13
 */
class VNavigationArray {
	private $arData;
	private $NavNum;
	private $NavPageNomer;
	private $NavPageCount;
	private $bAjax;

	public function __construct(array $arData, $bAjax=false){
		$this->bAjax=$bAjax;
		if (!empty($arData))
			$this->arData=$arData;
	}

	public function getNav(){

		$this->NavPageNomer=$this->arData['page'];
		$this->NavPageCount=$this->arData['pageCount'];
		$this->NavNum=1;

		$arDel=array("PAGEN_1", "SIZEN_1", "SHOWALL_1", "PHPSESSID");
		$strNavQueryString = DeleteParam($arDel);
		$sUrlPath = GetPagePath();
		$sUrlPath=preg_replace('#^\/rest#','',$sUrlPath);

		if($strNavQueryString <> "")
			$strNavQueryString = htmlspecialcharsbx("&".$strNavQueryString);

		$nPageWindow=$this->arData['pageWindow'];

		if($this->arData['page'] > floor($nPageWindow/2) + 1 && $this->arData['pageCount'] > $nPageWindow)
			$nStartPage = $this->arData['page'] - floor($nPageWindow/2);
		else
			$nStartPage = 1;

		if($this->arData['page'] <= $this->arData['pageCount'] - floor($nPageWindow/2) && $nStartPage + $nPageWindow-1 <= $this->arData['pageCount'])
			$nEndPage = $nStartPage + $nPageWindow - 1;
		else
		{
			$nEndPage = $this->arData['pageCount'];

			if($nEndPage - $nPageWindow + 1 >= 1)
				$nStartPage = $nEndPage - $nPageWindow + 1;
		}

		if ($this->bAjax){
			return array(
				'endPage'=>$nEndPage,
				'startPage'=>$nStartPage,
				'urlPath'=>$sUrlPath,
				'queryString'=>$strNavQueryString,
				'navNum'=>$this->NavNum,
				'pageNomer'=>$this->NavPageNomer,
				'pageCount'=>$this->NavPageCount
			);
		}else{
			ob_start();
			require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/estelife/system/pagenav.php';
			$sTemplate=ob_get_clean();
			return $sTemplate;
		}
	}
}