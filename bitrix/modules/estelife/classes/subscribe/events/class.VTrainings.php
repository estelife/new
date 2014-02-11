<?php
namespace subscribe\events;
use subscribe\factories\VTrainings as VTrainingsFactory;
use subscribe\mailers\VTrainings as VTrainingsMailer;
use subscribe\owners\VOwner;

/**
 * Вероятно я забыл оставить описание файла. Обратитесь на мыло за уточнениями.
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 * @since 11.02.14
 */
class VTrainings implements VAggregator {
	public function checkEvent(VEvent $obEvent){
		return $obEvent->getType()==2;
	}

	public function aggregateEvent(VOwner $obOwner, VEvent $obEvent){
		$obFactory=new VTrainingsFactory($obOwner,$obEvent);
		$obMailer=new VTrainingsMailer(
			$obOwner,
			$obFactory
		);
		$obMailer->send();
	}
}