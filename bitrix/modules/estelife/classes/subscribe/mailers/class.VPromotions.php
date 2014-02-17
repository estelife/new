<?php
namespace subscribe\mailers;
use subscribe\factories\VFactory;
use subscribe\owners\VOwner;

/**
 * Вероятно я забыл оставить описание файла. Обратитесь на мыло за уточнениями.
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 * @since 11.02.14
 */
class VPromotions extends VMailer {
	public function __construct(VOwner $obOwner,VFactory $obFactory){
		parent::__construct($obOwner,$obFactory);
	}

	public function send(){
		$arClinics=$this->obFactory->getElements();
		$sContent='';

		if(!empty($arClinics)){
			foreach($arClinics as $arClinic){
				$sContent.='<h2><a href="/cl/'.$arClinic['id'].'/">'.$arClinic['name'].'</a></h2>';

				foreach($arClinic['elements'] as $arElement)
					$sContent .= '<a href="/pr'.$arElement['id'].'/">'.$arElement['name'].'</a><br>';
			}
		}

		CEvent::Send("SEND_SUBSCRIBE_PROMOTIONS", "s1", array(
			'EMAIL_TO'=>$this->obOwner->getEmail(),
			'PROMOTIONS'=>$sContent
		),"Y",61);
	}
}