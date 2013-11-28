<?php
namespace reference;
use core\database\collections\VCollection;
use core\database\collections\VMeta;

/**
 * Вероятно я забыл оставить описание. Пишите на почту, дам комментарий.
 * @since 10.10.13
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 */
class VContacts extends VCollection {
	public function __construct() {
		parent::__construct('estelife_contacts',array(
			'id'=>array(
				'primary'=>true,
				'join'=>array(
					'estelife_clinic_contacts'=>array(
						'field'=>'id',
						'type'=>VMeta::ONE_TO_MANY
					)
				)
			)
		));
	}
}