<?php
namespace reference\services;
use core\database\collections\VCollection;
use core\database\collections\VMeta;

/**
 * Вероятно я забыл оставить описание. Пишите на почту, дам комментарий.
 * @since 09.10.13
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 */
class VMethods extends VCollection {
	public function __construct(){
		parent::__construct('estelife_methods',array(
			'id'=>array(
				'primary'=>true
			),
			'specialization_id'=>array(
				'join'=>array(
					'estelife_specializations'=>array(
						'field'=>'id',
						'type'=>VMeta::MANY_TO_ONE
					)
				)
			),
			'service_id'=>array(
				'join'=>array(
					'estelife_services'=>array(
						'field'=>'id',
						'type'=>VMeta::MANY_TO_ONE
					)
				)
			)
		));
	}
}