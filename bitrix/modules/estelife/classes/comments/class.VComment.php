<?php
namespace comments;
use core\database\mysql\VFilter;
use core\database\VDatabase;
use core\exceptions\VException;
use core\types\VArray;

/**
 *
 * @author Panait Vitaly <panait.v@yandex.ru>
 * @since 06.02.14
 */

final class VComment{

	public function __construct(){}

	/**
	 * Добавление комментария в базу
	 * @param $sText
	 * @param $nType
	 * @param $nElId
	 * @return bool|mixed
	 *
	 */
	public function setComment($sText, $nType, $nElId){
		if (empty($sText) || empty($nType) || empty($nElId))
			return false;

		$nId=0;

		global $USER;
		if ($USER->IsAuthorized()){
			$nUserId=$USER->GetID();
			$obComment=VDatabase::driver();
			$obQuery=$obComment->createQuery();
			$obQuery
				->builder()
				->from('estelife_comments')
				->value('text',$sText)
				->value('date_create',date('Y-m-d H:i:s', time()))
				->value('active', 1)
				->value('moderate', 0)
				->value('user_id', $nUserId)
				->value('type', $nType)
				->value('element_id', $nElId);
			$nId=$obQuery->insert()->insertId();
		}

		return $nId;
	}

	/**
	 * Выставляет куки для комментариев
	 * @param $nId
	 * @param $sName
	 * @param $nType
	 * @param $nElId
	 */

	private function setCookieAndSession($nId, $sName, $nType, $nElId){

		if (isset($_COOKIE['estelife_comments']) && !empty($_COOKIE['estelife_comments'])){
			$arCookie = unserialize($_COOKIE['estelife_comments']);
		}else
			$arCookie = array();

		if (isset($_SESSION['estelife_comments'])){
			$arSession = unserialize($_SESSION['estelife_comments']);
		}else
			$arSession = array();

		$sMD5=md5($nId.$sName.$nType.$nElId.'solonatakaiasol');

		$arCookie[]=$arSession[]=$sMD5;

		setcookie('estelife_comments', serialize($arCookie), time() + 12*60*60*24*30, '/');
		$_SESSION['estelife_comments']=serialize($arSession);
	}

	/**
	 * Добавление ответа на комментарий
	 * TODO: Дописать метод если будут древовидные комментарии
	 */
	public function setAnswer(){

	}

	/**
	 * Получение всех комментариев
	 * @param $nType
	 * @param $nElId
	 * @param $nCount
	 * @return array|bool
	 */
	public function getComments($nType, $nElId, $nCount=false){
		if (empty($nType) || empty($nElId))
			return false;

		$obComment=VDatabase::driver();
		$obQuery=$obComment->createQuery();
		$obQuery
			->builder()
			->from('estelife_comments', 'ec');
		$obJoin=$obQuery->builder()->join();
		$obJoin->_left()
			->_from('ec', 'user_id')
			->_to('user', 'ID', 'u');
		$obQuery->builder()
			->sort('date_create', 'asc')
			->field('ec.text')
			->field('ec.id')
			->field('ec.date_create')
			->field('u.NAME', 'name')
			->field('u.LAST_NAME', 'last_name')
			->field('u.LOGIN', 'login')
			->filter()
			->_eq('ec.type', $nType)
			->_eq('ec.element_id', $nElId)
			->_eq('ec.active', 1)
			->_eq('ec.moderate', 0);
		if ($nCount>0)
			$obQuery->builder()->slice(0, $nCount);

		return $obQuery->select()->all();
	}

	/**
	 * Получение комментариев пользователя
	 * @param $nUserId
	 * @return array|bool
	 */
	public function getUserComments($nUserId){
		if (empty($nUserId))
			return false;

		$obComment=VDatabase::driver();
		$obQuery=$obComment->createQuery();
		$obQuery
			->builder()
			->from('estelife_comments')
			->sort('date_create', 'desc')
			->sort('type', 'asc')
			->filter()
			->_eq('user_id', $nUserId);

		return $obQuery->select()->all();
	}

	/**
	 * Модерация комментария
	 * @param $nCommentId
	 * @return bool
	 */
	public function setModerate($nCommentId){
		if (empty($nCommentId))
			return false;

		$obComment=VDatabase::driver();
		$obQuery=$obComment->createQuery();
		$obQuery
			->builder()
			->from('estelife_comments')
			->value('moderate', 1)
			->filter()
				->_eq('id', $nCommentId);

		return ($obQuery->update()) ? true : false;
	}

	/**
	 * Удаление комментария
	 * @param $nCommentId
	 * @return bool
	 */
	public function delComment($nCommentId){
		if (empty($nCommentId))
			return false;

		$obComment=VDatabase::driver();
		$obQuery=$obComment->createQuery();
		$obQuery
			->builder()
			->from('estelife_comments')
			->filter()
			->_eq('id', $nCommentId);

		return ($obQuery->delete()) ? true : false;
	}

	/**
	 * Общий метод для получения количества
	 * @param $nType
	 * @param $nElId
	 * @param bool $sGroup
	 * @return bool|int
	 */
	private function getCount($nType, $nElId, $sGroup=false){
		if (empty($nType) || empty($nElId))
			return false;

		$obComment=VDatabase::driver();
		$obQuery=$obComment->createQuery();
		$obQuery
			->builder()
			->from('estelife_comments')
			->filter()
			->_eq('type', $nType)
			->_eq('element_id', $nElId)
			->_eq('active', 1)
			->_eq('moderate', 0);

		if (!empty($sGroup))
			$obQuery->builder()->group($sGroup);

		return $obQuery->select()->count();
	}

	/**
	 * Получение количества комментариев
	 * @param $nType
	 * @param $nElId
	 * @return bool|int
	 */

	public function getCountComments($nType, $nElId){
		return $this->getCount($nType, $nElId);
	}

	public function getCountUnreadComments(){
		$obComment=VDatabase::driver();
		$obQuery=$obComment->createQuery();
		$obQuery
			->builder()
			->from('estelife_comments')
			->filter()
			->_eq('moderate', 0);

		return $obQuery->select()->count();
	}

	/**
	 * Получение количества пользователей для конкретного типа
	 * @param $nType
	 * @param $nElId
	 * @return bool|int
	 */
	public function getCountUsers($nType, $nElId){
		return $this->getCount($nType, $nElId, 'user_id');
	}

}