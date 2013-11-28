<?php
namespace reference\services;
use core\database\collections\VCollection;
use core\database\collections\VMeta;

/**
 * Вероятно я забыл оставить описание. Пишите на почту, дам комментарий.
 * @since 09.10.13
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 */
class VSpecs extends VCollection {
	public function __construct(){
		parent::__construct('estelife_specializations',array(
			'id'=>array(
				'type'=>'int',
				'primary'=>true,
				'join'=>array(
					'estelife_services'=>array(
						'field'=>'specialization_id',
						'type'=>VMeta::ONE_TO_MANY
					),
					'estelife_service_concreate'=>array(
						'field'=>'specialization_id',
						'type'=>VMeta::ONE_TO_MANY
					)
				)
			)
		));
	}
}