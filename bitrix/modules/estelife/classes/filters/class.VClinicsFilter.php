<?php
namespace filters;

/**
 *
 * @author Maxim Shlemarev <shlemarev@gmail.com>
 * @since 30.01.14
 */

class VClinicsFilter implements VCreator{



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



		$obQuery = new \filters\VQuery('clinics');
		$obSession = new \filters\VSession('clinics');

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

		$arResultParams = array();
		$nApprove = 1;
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

		$obSession = new \filters\VSession('clinics');


		if(isset($this->params)){

			foreach($this->params as $sParams){
				$nCount++;
			}

			if($nCount == 1 && isset($this->params['city']) && $this->params['city'] == 'all'){
				unset($_SESSION['filter']['clinics']);
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

				/*foreach($this->params as $kParam=>$sParam){

					if(!array_key_exists($kParam, $this->fields)){
						$nApprove = 0;
					}else{
						$obSession->setParam($kParam,$sParam);
						$arResultParams[$kParam] = $sParam;
					}
				}*/
				/*if(!isset($arResultParams['name'])){
					$obSession->setParam('name','');
					$arResultParams['name'] = '';
				}*/
			}


				return $arParamsResult;

		}else{
			return $arEmptyParams;
		}
	}

}