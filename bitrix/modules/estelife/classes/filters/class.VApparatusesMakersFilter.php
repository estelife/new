<?php
namespace filters;

/**
 *
 * @author Maxim Shlemarev <shlemarev@gmail.com>
 * @since 30.01.14
 */

class VApparatusesMakersFilter implements VCreator{


	public function __construct(){

		$this->fields = array(
			'name'=>'name',
			'country'=>'country',
		);

		$obQuery = new \filters\VQuery('apparatuses_makers');
		$obSession = new \filters\VSession('apparatuses_makers');

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
			'country'=>'',
		);

		$obSession = new \filters\VSession('apparatuses_makers');


		if(isset($this->params)){

			foreach($this->params as $sParams){
				$nCount++;
			}

			if($nCount == 1 && isset($this->params['country']) && $this->params['country'] == 'all'){
				unset($_SESSION['filter']['apparatuses_makers']);
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