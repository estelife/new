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

	public function __construct(){
	}

	/**
	 * Добавление комментария в базу
	 * @param $sText
	 * @param $sName
	 * @param $nType
	 * @param $nElId
	 * @return bool|mixed
	 *
	 */
	public function setComment($sText, $sName, $nType, $nElId){
		if (empty($sText) || empty($sName) || empty($nType) || empty($nElId))
			return false;

		global $USER;
		if ($USER->IsAuthorized())
			$nUserId=$USER->GetID();
		else
			$nUserId=0;

		$obComment=VDatabase::driver();
		$obQuery=$obComment->createQuery();
		$obQuery
			->builder()
			->from('estelife_comments')
			->value('text',$sText)
			->value('date_create',date('Y-m-d H:i:s', time()))
			->value('active', 1)
			->value('moderate', 0)
			->value('name', $sName)
			->value('user_id', $nUserId)
			->value('type', $nType)
			->value('element_id', $nElId);
		if ($nId=$obQuery->insert()->insertId())
			$this->setCookieAndSession($nId, $sName, $nType, $nElId);

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

		if (isset($_SESSION['estelife_comments']) && !empty($_SESSION['estelife_comments']))
			$arDataSession=unserialize($_SESSION['estelife_comments']);
		else
			$arDataSession=array();

		if (isset($_COOKIE['estelife_comments']) && !empty($_COOKIE['estelife_comments']))
			$arDataCookie=unserialize($_COOKIE['estelife_comments']);
		else
			$arDataCookie=array();

		$arDataGuest=array_unique(array_merge($arDataSession, $arDataCookie));

		$obComment=VDatabase::driver();
		$obQuery=$obComment->createQuery();
		$obQuery
			->builder()
			->from('estelife_comments')
			->sort('date_create', 'desc');
		$obFilter=$obQuery->builder()->filter();
			if (!empty($arDataGuest)){
				$obFilter->_or()
					->_eq('active', 1)
					->_in(
						$obQuery->builder()->_md5('id','name',$nType.$nElId.'solonatakaiasol'),
						$arDataGuest
					);
			}
			$obFilter->_or()
				->_eq('type', $nType)
				->_eq('element_id', $nElId)
				->_eq('active', 1)
				->_eq('moderate', 1);
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

		if (isset($_SESSION['estelife_comments']) && !empty($_SESSION['estelife_comments']))
			$arDataSession=unserialize($_SESSION['estelife_comments']);

		if (isset($_COOKIE['estelife_comments']) && !empty($_COOKIE['estelife_comments']))
			$arDataCookie=unserialize($_COOKIE['estelife_comments']);

		$arDataGuest=array_unique(array_merge($arDataSession, $arDataCookie));

		$obComment=VDatabase::driver();
		$obQuery=$obComment->createQuery();
		$obQuery
			->builder()
			->from('estelife_comments');
		$obFilter=$obQuery->builder()->filter();
		if (!empty($arDataGuest)){
			$obFilter->_or()
				->_eq('active', 1)
				->_in(
					$obQuery->builder()->_md5('id','name',$nType.$nElId.'solonatakaiasol'),
					$arDataGuest
				);
		}
		$obFilter->_or()
			->_eq('type', $nType)
			->_eq('element_id', $nElId)
			->_eq('active', 1)
			->_eq('moderate', 1);

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

	/**
	 * Получение количества пользователей для конкретного типа
	 * @param $nType
	 * @param $nElId
	 * @return bool|int
	 */
	public function getCountUsers($nType, $nElId){
		return $this->getCount($nType, $nElId, 'name');
	}
}