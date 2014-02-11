<?php
namespace filters;

/**
 *
 * @author Maxim Shlemarev <shlemarev@gmail.com>
 * @since 30.01.14
 */

class VTrainingsFilter implements VCreator{


	public function __construct(){

		$this->fields = array(
			'city'=>'city',
			'direction'=>'direction',
			'spec'=>'spec',
			'date_from'=>'date_from',
			'date_to'=>'date_to',
		);

		$obQuery = new \filters\VQuery('trainings');
		$obSession = new \filters\VSession('trainings');

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
			'city'=>'358',
			'direction'=>'',
			'spec'=>'',
			'date_from'=>'',
			'date_to'=>'',
		);

		$obSession = new \filters\VSession('trainings');


		if(isset($this->params)){

			foreach($this->params as $sParams){
				$nCount++;
			}

			if($nCount == 1 && isset($this->params['city']) && $this->params['city'] == 'all'){
				unset($_SESSION['filter']['trainings']);
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