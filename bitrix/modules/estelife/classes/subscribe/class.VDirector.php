<?php
/**
 *
 * @author Maxim Shlemarev <shlemarev@gmail.com>
 * @since 30.01.14
 */

namespace subscribe;

class VDirector {

	public function __construct(){

	}


	public function TrainingsSend($arElements,$sUserEmail){

		/*print_r($arElements);*/
		$obData = \core\database\VDatabase::driver();

		$sMailTargetEvents = "";
		$sMailComplexEvents = "";
		$campaniesTargetAr = array();
		$campaniesComplexAr = array();

		$i = 0;
		$k = 0;

		foreach($arElements as $arKey=>$arElement){


			if($arKey == 'target'){
				foreach($arElement as $element){
					$campaniesTargetAr[$i] = $element['company_id'];
					$i++;
				}
			}else if($arKey == 'complex'){
				foreach($arElement as $element){
					$campaniesComplexAr[$k] = $element['company_id'];
					$k++;
				}
			}
		}


		foreach($campaniesTargetAr as $arTargetKey=>$arTargetValue){

			$obQueryClinic = $obData->createQuery();
			$obQueryClinic->builder()->from('estelife_companies');
			$obQueryClinic->builder()->filter()->_eq('id',$arTargetValue);
			$arCompanyData = $obQueryClinic->select()->assoc();

			$sCompanyName = $arCompanyData['name'];

			$sMailTargetEvents .=$sCompanyName.'<br>';

			foreach($arElements['target'] as $arKey=>$arTargetElement){
				$sTEvId = $arTargetElement['id'];
				$sTEvName = $arTargetElement['short_name'];

				if($arTargetValue == $arTargetElement['company_id']){
					$sMailTargetEvents .= "<a href='/tr".$sTEvId."/'>$sTEvName</a><br>";
				}
			}
		}

		foreach($campaniesComplexAr as $arComplexKey=>$arComplexValue){

			$obQueryClinic = $obData->createQuery();
			$obQueryClinic->builder()->from('estelife_companies');
			$obQueryClinic->builder()->filter()->_eq('id',$arComplexValue);
			$arCompanyData = $obQueryClinic->select()->assoc();

			$sCompanyName = $arCompanyData['name'];

			$sMailComplexEvents .=$sCompanyName.'<br><br>';

			foreach($arElements['complex'] as $arKey=>$arComplexElement){

				$sCEvId = $arComplexElement['id'];
				$sCEvName = $arComplexElement['short_name'];


				if($arComplexValue == $arComplexElement['company_id']){

					$sMailComplexEvents .= "<a href='/tr".$sCEvId."/'>$sCEvName</a><br><br>";
				}
			}

		}

		if(empty($arElements['target']) && empty($arElements['complex'])){
			return $arFields = array();
		}else{

			$arFields = array(
				'EMAIL_TO'=>$sUserEmail,
				'TARGET_EVENTS'=>$sMailTargetEvents,
				'COMPLEX_EVENTS'=>$sMailComplexEvents,
			);

			return $arFields;


		}

	}


	public function ClinicSend($arElements,$sUsemEmail){

		$obData = \core\database\VDatabase::driver();

		$sMailTargetEvents = "";
		$sMailComplexEvents = "";

		$i = 0;
		$k = 0;

		foreach($arElements as $arKey=>$arElement){



			if($arKey == 'target'){
				foreach($arElement as $key=>$value){

					$obQueryClinic = $obData->createQuery();
					$obQueryClinic->builder()->from('estelife_clinics');
					$obQueryClinic->builder()->filter()->_eq('id',$key);
					$arClinicData = $obQueryClinic->select()->assoc();

					$sClinicName = $arClinicData['name'];

					$sMailTargetEvents .=$sClinicName.'<br>';

					foreach($value as $event){
						$sEventName = $event['name'];
						$nEventId = $event['id'];

						$sMailTargetEvents .="<a href='/pr".$nEventId."/'>$sEventName</a><br>";

					}

				}
			}else if($arKey == 'complex'){
				foreach($arElement as $key=>$value){

					$obQueryClinic = $obData->createQuery();
					$obQueryClinic->builder()->from('estelife_clinics');
					$obQueryClinic->builder()->filter()->_eq('id',$key);
					$arClinicData = $obQueryClinic->select()->assoc();

					$sClinicName = $arClinicData['name'];

					$sMailComplexEvents .=$sClinicName.'<br><br>';

					foreach($value as $event){
						$sEventName = $event['name'];
						$nEventId = $event['id'];

						$sMailComplexEvents .="<a href='/pr".$nEventId."/'>$sEventName</a><br><br>";

					}

				}
			}
		}


		if(empty($arElements['target']) && empty($arElements['complex'])){

			return $arFields = array();

		}else{

			$arFields = array(
				'EMAIL_TO'=>$sUsemEmail,
				'TARGET_EVENTS'=>$sMailTargetEvents,
				'COMPLEX_EVENTS'=>$sMailComplexEvents,
			);

			return $arFields;

		}

	}

}