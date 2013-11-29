<?php
namespace clinics;
use core\database\collections\VCollection;
use core\database\collections\VMeta;

/**
 * Вероятно я забыл оставить описание. Пишите на почту, дам комментарий.
 * @since 09.10.13
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 */
class VClinics extends VCollection {
	private $obHours;
	private $obAkzii;
	private $obContacts;
	private $obGallery;
	private $obPhotos;
	private $obServices;

	public function __construct(){
		parent::__construct('estelife_clinics',array(
			'id'=>array(
				'primary'=>true,
				'join'=>array(
					'estelife_busy_hours'=>array(
						'field'=>'clinic_id',
						'type'=>VMeta::ONE_TO_MANY
					),
					'estelife_clinic_contacts'=>array(
						'field'=>'clinic_id',
						'type'=>VMeta::ONE_TO_MANY
					),
					'estelife_clinic_akzii'=>array(
						'field'=>'clinic_id',
						'type'=>VMeta::ONE_TO_MANY
					),
					'estelife_clinic_photos'=>array(
						'field'=>'clinic_id',
						'type'=>VMeta::ONE_TO_ONE
					),
					'estelife_clinic_gallery'=>array(
						'field'=>'clinic_id',
						'type'=>VMeta::ONE_TO_MANY
					),
					'estelife_clinic_services'=>array(
						'field'=>'clinic_id',
						'type'=>VMeta::ONE_TO_MANY
					),
					'estelife_clinics'=>array(
						'field'=>'clinic_id',
						'type'=>VMeta::ONE_TO_MANY
					)
				)
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

		$this->obHours=new VBusyHours();
		$this->obAkzii=new VAkzii();
		$this->obContacts=new VContacts();
		$this->obGallery=new VGallery();
		$this->obServices=new VServices();
		$this->obPhotos=new VPhotos();
	}

	public function hours(){
		return $this->obHours;
	}

	public function akzii(){
		return $this->obAkzii;
	}

	public function contacts(){
		return $this->obContacts;
	}

	public function gallery(){
		return $this->obGallery;
	}

	public function services(){
		return $this->obServices;
	}

	public function photos(){
		return $this->obPhotos;
	}
}