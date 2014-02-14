<?php
namespace subscribe\events;
use subscribe\factories\VPromotions as VPromotionsFactory;
use subscribe\mailers\VPromotions as VPromotionsMailer;
use subscribe\owners\VOwner;

/**
 * Вероятно я забыл оставить описание файла. Обратитесь на мыло за уточнениями.
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 * @since 11.02.14
 */
class VPromotions implements VAggregator {
	public function checkEvent(VEvent $obEvent){
		return $obEvent->getType()==1;
	}

	public function aggregateEvent(VOwner $obOwner, VEvent $obEvent){
		$obFactory=new VPromotionsFactory($obOwner,$obEvent);
		$obMailer=new VPromotionsMailer(
			$obOwner,
			$obFactory
		);
		$obMailer->send();
	}
}