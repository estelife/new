<?php
namespace orm\items;
use lists\VList;

/**
 * Вероятно я забыл оставить описание файла. Обратитесь на мыло за уточнениями.
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 * @since 02.12.13
 */
class VClinic extends VItem {
	/**
	 * @field (id,int)
	 * @length (11)
	 * @key (primary)
	 * @join (one_to_many, VClinic)
	 */
	protected $id;

	/**
	 * @field (name,string)
	 * @length (255)
	 * @default
	 */
	protected $name;

	/**
	 * @field (active,int)
	 * @length (1)
	 * @default 0
	 */
	protected $active;

	/**
	 * @field (recomended,int)
	 * @length (1)
	 * @default 0
	 */
	protected $recomended;

	/**
	 * @field (preview_text,text)
	 * @default
	 */
	protected $preview_text;

	/**
	 * @field (detail_text,text)
	 * @default
	 */
	protected $detail_text;

	/**
	 * @field (detail_text,text)
	 * @default
	 */
	protected $dop_text;

	/**
	 * @field (city_id,int)
	 * @length (11)
	 * @join (one_to_one, VCity)
	 * @default 0
	 */
	protected $city;

	/**
	 * @field (metro_id,int)
	 * @length (11)
	 * @join (one_to_one, VMetro)
	 * @default 0
	 */
	protected $metro;

	/**
	 * @field (name,varchar)
	 * @length (255)
	 * @default
	 */
	protected $address;

	/**
	 * @field (latitude,float)
	 * @length (11,9)
	 * @default 0
	 */
	protected $latitude;

	/**
	 * @field (longitude,float)
	 * @length (11,9)
	 * @default 0
	 */
	protected $longitude;

	/**
	 * @field (logo_id,int)
	 * @length (11)
	 * @default 0
	 */
	protected $logo;

	/**
	 * @field (clinic_id,int)
	 * @length (11)
	 * @join (one_to_one, VClinic)
	 * @default 0
	 */
	protected $clinic;

	/**
	 * @table (estelife_clinics)
	 * @engine (InnoDB)
	 */
	public function __construct($sField=false,$mValue=false){
		parent::__construct($sField=false,$mValue=false);
	}
}