<?php
namespace filters;

/**
 *
 * @author Maxim Shlemarev <shlemarev@gmail.com>
 * @since 30.01.14
 */

class VPromotionsFilter implements VCreator{



	public function __construct(){

		$this->fields = array(
			'name'=>'name',
			'city'=>'city',
			'metro'=>'metro',
			'spec'=>'spec',
			'service'=>'service',
			'method'=>'method',
			'concreate'=>'concreate',
		);



		$obQuery = new \filters\VQuery('aktii');
		$obSession = new \filters\VSession('aktii');

		if(!empty($_GET)){
			$arParams = $obQuery->getAllParams();
			$this->params = $arParams;
		}else{
			if(!empty($_SESSION['filter'])){
				$arParams = $obSession->getAllParams();
				$this->params = $arParams;
			}else{
				//throw new Error('params not found!');
			}
		}

	}

	public function getParams(){

		$nCount = 0;

		$arEmptyParams = array(
			'name'=>'',
			'city'=>'358',
			'metro'=>'',
			'spec'=>'',
			'service'=>'',
			'method'=>'',
			'concreate'=>'',
		);

		$obSession = new \filters\VSession('aktii');


		if(isset($this->params)){

			foreach($this->params as $sParams){
				$nCount++;
			}

			if($nCount == 1 && isset($this->params['city']) && $this->params['city'] == 'all'){
				unset($_SESSION['filter']['aktii']);
			}else{

				$arParamsResult = array();

				foreach($this->fields as $sVal){

					if(isset($this->params[$sVal])){
						$obSession->setParam($sVal, $this->params[$sVal]);
						$arParamsResult[$sVal] = $this->params[$sVal];
					}else{
						$obSession->setParam($sVal, '');
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