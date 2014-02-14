<?php
namespace subscribe\mailers;
use subscribe\factories\VFactory;
use subscribe\owners\VOwner;

/**
 * Вероятно я забыл оставить описание файла. Обратитесь на мыло за уточнениями.
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 * @since 11.02.14
 */
abstract class VMailer {
	protected $obOwner;
	protected $obFactory;

	public function __construct(VOwner $obOwner,VFactory $obFactory){
		$this->obOwner=$obOwner;
		$this->obFactory=$obFactory;
	}

	abstract public function send();
}