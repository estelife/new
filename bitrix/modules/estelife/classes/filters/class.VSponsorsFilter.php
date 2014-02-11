<?php
namespace filters;

/**
 *
 * @author Maxim Shlemarev <shlemarev@gmail.com>
 * @since 30.01.14
 */

class VSponsorsFilter implements VCreator{

	public function __construct(){

		$this->fields = array(
			'name'=>'name',
			'country'=>'country',
			'city'=>'city',
		);

		$obQuery = new \filters\VQuery('sponsors');
		$obSession = new \filters\VSession('sponsors');

		if(!empty($_GET)){
			$arParams = $obQuery->getAllParams();
			$this->params = $arParams;
		}else{
			if($obSession->getAllParams()){
				$arParams = $obSession->getAllParams();
				$this->params = $arParams;
			}else{

			}
		}
	}

	public function getParams(){
		$nCount = 0;

		$arEmptyParams = array(
			'name'=>'',
			'country'=>'357',
			'city'=>'',
		);

		$obSession = new \filters\VSession('sponsors');

		if(isset($this->params)){

			foreach($this->params as $sParams){
				$nCount++;
			}

			if($nCount == 2 && isset($this->params['city']) && isset($this->params['country']) && $this->params['city'] == 'all' && $this->params['country'] == 'all'){
				unset($_SESSION['filter']['sponsors']);
			}else{
				$arParamsResult = array();

				foreach($this->fields as $sVal){

					if(isset($this->params[$sVal])){
						$obSession->setParam($sVal, $this->params[$sVal]);
						$arParamsResult[$sVal] = $this->params[$sVal];
					}else{
						$arParamsResult[$sVal] = '';
					}
				}
			}
			return $arParamsResult;
		}else{
			return $arEmptyParams;
		}
	}
}