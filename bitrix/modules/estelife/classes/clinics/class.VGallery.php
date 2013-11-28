<?php
namespace clinics;
use core\database\collections\VCollection;
use core\database\collections\VMeta;

/**
 * Вероятно я забыл оставить описание. Пишите на почту, дам комментарий.
 * @since 10.10.13
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 */
class VGallery extends VCollection {
	public function __construct(){
		parent::__construct('estelife_clinic_gallery',array(
			'id'=>array(
				'primary'=>true
			),
			'clinic_id'=>array(
				'join'=>array(
					'estelife_clinics'=>array(
						'field'=>'id',
						'type'=>VMeta::MANY_TO_ONE
					)
				)
			)
		));
	}
}