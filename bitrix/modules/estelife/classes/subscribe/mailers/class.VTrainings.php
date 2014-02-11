<?php
namespace subscribe\mailers;
use subscribe\factories\VFactory;
use subscribe\owners\VOwner;

/**
 * Вероятно я забыл оставить описание файла. Обратитесь на мыло за уточнениями.
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 * @since 11.02.14
 */
class VTrainings extends VMailer {
	public function __construct(VOwner $obOwner,VFactory $obFactory){
		parent::__construct($obOwner,$obFactory);
	}

	public function send(){
		$arCompanies=$this->obFactory->getElements();
		$sContent='';

		if(!empty($arCompanies)){
			foreach($arCompanies as $arCompany){
				$sContent.='<h2><a href="/tc/'.$arCompany['id'].'/">'.$arCompany['name'].'</a></h2>';

				foreach($arCompany['elements'] as $arElement)
					$sContent .= '<a href="/pr'.$arElement['id'].'/">'.$arElement['name'].'</a><br>';
			}
		}

		CEvent::Send("SEND_SUBSCRIBE_TRAINING", "s1", array(
			'EMAIL_TO'=>$this->obOwner->getEmail(),
			'TRAININGS'=>$sContent
		),"Y",63);
	}
}