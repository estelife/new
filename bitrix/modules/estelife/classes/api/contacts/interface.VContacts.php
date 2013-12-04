<?php
namespace lists\contacts;

/**
 * Вероятно я забыл оставить описание файла. Обратитесь на мыло за уточнениями.
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 * @since 02.12.13
 */
interface VContacts {
	public function getPhones();
	public function getFaxes();
	public function getEmails();
	public function getAddress();
	public function getCountry();
	public function getCity();
	public function getFullAddress();
}