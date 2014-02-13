<?php
namespace filters;

/**
 *
 * @author Maxim Shlemarev <shlemarev@gmail.com>
 * @since 30.01.14
 */

class VTrainingsCentersFilter implements VDecorator{

	public function __construct(){

		$this->fields = array(
			'name'=>'name',
			'city'=>'city',
		);

		$obQuery = new \filters\VQuery('trainigs_center');
		$obSession = new \filters\VSession('trainigs_center');

		if(!empty($_GET)){
			$arParams = $obQuery->getAllParams();
			$this->params = $arParams;
		}else{
			if($obSession->getAllParams()){

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
		);

		$obSession = new \filters\VSession('trainigs_center');

		if(isset($this->params)){

			foreach($this->params as $sParams){
				$nCount++;
			}

			if($nCount == 1 && isset($this->params['city']) && $this->params['city'] == 'all'){
				unset($_SESSION['filter']['trainigs_center']);
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