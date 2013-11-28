<?php
namespace akzii;
use core\database\collections\VCollection;
use core\database\collections\VMeta;

/**
 * Вероятно я забыл оставить описание. Пишите на почту, дам комментарий.
 * @since 10.10.13
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 */
class VAkzii extends VCollection {
	private $obPhotos;

	public function __construct(){
		parent::__construct('estelife_akzii',array(
			'id'=>array(
				'primary'=>true,
				'join'=>array(
					'estelife_clinic_akzii'=>array(
						'field'=>'akzii_id',
						'type'=>VMeta::ONE_TO_MANY
					),
					'akzii_photos'=>array(
						'field'=>'akzii_id',
						'type'=>VMeta::ONE_TO_MANY
					)
				)
			)
		));

		$this->obPhotos=new VPhotos();
	}

	public function photos(){
		return $this->obPhotos;
	}
}