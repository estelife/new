<?php
namespace like;
use core\database\mysql\VFilter;
use core\database\VDatabase;
use core\exceptions\VException;

final class VLike{
	const ARTICLE = 1;
	const CLINIC = 2;
	const PROMOTION = 3;
	const EVENT = 4;
	const SPONSOR = 5;
	const TRAINING = 6;
	const CENTERS = 7;
	const PREPARATION = 8;
	const APPARATUS = 9;
	const PREP_MAKERS = 10;
	const APP_MAKERS = 11;
	protected $type;
	protected $elId;
	protected $typeLike;
	protected $user_id;
	protected $ip;

	private function __clone(){}
	public function __construct($nType){
		global $USER;

		if (!empty($nType))
			$this->type = $nType;

		if ($USER->IsAuthorized()){
			$this->user_id = $USER->GetID();
			$this->ip = 0;
		}else{
			$this->user_id = 0;
			$this->ip = $_SERVER['REMOTE_ADDR'];
		}
	}

	public function like($elId, $typeLike, $hash=false){
		if (empty($elId))
			throw new VException('Не указан Id записи');

		if (empty($typeLike))
			throw new VException('Не указан тип лайка');

		$this->elId = $elId;
		$this->typeLike = $typeLike;

		if ($hash)
			$arUserLike = $this->checkLike($hash);
		else
			$arUserLike = $this->checkLike();


		if (empty($arUserLike)){
			$arLikes = $this->addNewLike();
		}else{
			$arLikes = $this->updateLike($arUserLike);
		}

		return $arLikes;
	}

	public function getOnlyLikes($elId){
		if (empty($elId))
			throw new VException('Не указан Id записи');

		$obLike=VDatabase::driver();
		$obQuery=$obLike->createQuery();
		$obQuery->builder()
			->from('estelife_likes');
		if (is_array($elId)){
			$obQuery->builder()->filter()
					->_in('element_id', $elId);
		}
		$obQuery->builder()->filter()->_eq('type', $this->type);
		$arLikes = $obQuery->select()->all();
		if (!empty($arLikes)){
			foreach ($arLikes as $val){
				$arNewLikes[$val['element_id']] = $val;
			}
			unset($arLikes);
		}

		return $arNewLikes;
	}

	public function getLikes($elId, $hash=false){
		if (empty($elId))
			throw new VException('Не указан Id записи');

		$this->elId=$elId;

		$obLike=VDatabase::driver();

		//проверка кук
		$arUserLike=$this->checkCookie($hash);

		if (!$arUserLike){
			$obQuery=$obLike->createQuery();
			$obQuery->builder()->from('estelife_likes', 'el');
			$obJoin=$obQuery->builder()->join();
			if($this->user_id>0){
				$obJoin->_left()
					->_from('el','id')
					->_to('estelife_user_likes','like_id','eul')
					->_cond()->_eq('eul.user_id', $this->user_id);
			}else{
				$obJoin->_left()
					->_from('el','id')
					->_to('estelife_user_likes','like_id','eul')
					->_cond()->_eq('eul.ip', $this->ip);
			}
			$obQuery->builder()->filter()
				->_eq('el.element_id', $this->elId)
				->_eq('el.type', $this->type);
			$obQuery->builder()
				->field('el.element_id', 'element_id')
				->field('el.type', 'type')
				->field('el.countLike', 'countLike')
				->field('el.countDislike', 'countDislike')
				->field('eul.type', 'typeLike');
			$arLikes = $obQuery->select()->assoc();

		}else{
			$obQuery=$obLike->createQuery();
			$obQuery->builder()->from('estelife_likes');
			$obQuery->builder()->filter()
				->_eq('element_id', $this->elId)
				->_eq('type', $this->type);
			$arLikes = $obQuery->select()->assoc();
			$arLikes['typeLike'] = $arUserLike['type'];
		}

		if (empty($arLikes)){
			$arLikes = array(
				'element_id'=>$this->elId,
				'typeLike'=>$this->typeLike,
				'type'=>$this->type,
				'countLike'=>0,
				'countDislike'=>0
			);
		}

		return $arLikes;
	}


	public function checkLike($hash=false){
		$obLike=VDatabase::driver();
		//проверка кук
		$arUserLike=$this->checkCookie($hash);

		if (!$arUserLike){
			$obQuery=$obLike->createQuery();
			$obQuery->builder()->from('estelife_likes');
			$obFilter=$obQuery->builder()->filter();
			$obFilter->_eq('element_id', $this->elId);
			$obFilter->_eq('type', $this->type);
			$arGlobalLike = $obQuery->select()->assoc();

			if (!empty($arGlobalLike)){
				$obQuery=$obLike->createQuery();
				$obQuery->builder()->from('estelife_user_likes');
				$obFilter=$obQuery->builder()->filter();
				$obFilter->_eq('like_id', $arGlobalLike['id']);
				if($this->user_id>0){
					$obFilter->_eq('user_id', $this->user_id);
				}else{
					$obFilter->_eq('ip', $this->ip);
				}
				$arUserLike = $obQuery->select()->assoc();
			}else{
				$arUserLike=false;
			}
		}

		return $arUserLike;
	}


	private function addNewLike(){
		$obLike=VDatabase::driver();

		$obQuery = $obLike->createQuery();
		$obQuery->builder()
			->from('estelife_likes')
			->filter()
				->_eq('element_id', $this->elId)
				->_eq('type', $this->type);
		$arLike = $obQuery->select()->assoc();


		if (empty($arLike)){
			$obQuery = $obLike->createQuery();
			$obQuery->builder()
				->from('estelife_likes')
				->value('element_id', $this->elId)
				->value('type', $this->type);
			if ($this->typeLike == 1){
				$nCountLike = 1;
				$nCountDislike = 0;
				$obQuery->builder()->value('countLike', $nCountLike);
				$obQuery->builder()->value('countDislike', $nCountDislike);
			}else{
				$nCountLike = 0;
				$nCountDislike = 1;
				$obQuery->builder()->value('countLike', $nCountLike);
				$obQuery->builder()->value('countDislike', $nCountDislike);
			}
			$nLikeId = $obQuery->insert()->insertId();
			$arLike = array(
				'element_id'=>$this->elId,
				'type'=> $this->type,
				'countLike'=>$nCountLike,
				'countDislike'=>$nCountDislike,
			);
		}else{
			$obQuery = $obLike->createQuery();
			$obQuery->builder()
				->from('estelife_likes');
			if ($this->typeLike == 1){
				$arLike['countLike']++;
			}else{
				$arLike['countDislike']++;
			}
			$obQuery->builder()
				->value('countLike', $arLike['countLike'])
				->value('countDislike', $arLike['countDislike'])
				->filter()
					->_eq('id', $arLike['id']);
			$obQuery->update();
			$nLikeId = $arLike['id'];
		}

		if (!$nLikeId)
			throw new VException('Ошибка добавления нового лайка в likes');

		//Добавление в таблицу лайков пользователя
		$obQuery = $obLike->createQuery();
		$obQuery->builder()
			->from('estelife_user_likes')
			->value('like_id', $nLikeId);
		if ($this->user_id>0){
			$obQuery->builder()->value('user_id', $this->user_id);
		}else{
			$obQuery->builder()->value('ip', $this->ip);
		}
		$obQuery->builder()->value('type', $this->typeLike);
		$nUserLikeId = $obQuery->insert()->insertId();

		if (!$nUserLikeId)
			throw new VException('Ошибка добавления нового лайка пользователя');

		//Устанвока КУК
		if ($this->user_id<=0){
			$arLike['md5']=md5($nUserLikeId.$nLikeId.$this->user_id.$this->ip.$this->typeLike.'estelifesoltlike');
			$this->setCookie($arLike['md5'], true);
		}

		return $arLike;
	}


	private function setCookie($sHash=false, $bFlag=false){
		if (isset($_COOKIE['estelife_likes']) && !empty($_COOKIE['estelife_likes'])){
			$sCookie = preg_replace('/id'.$this->elId.$this->type.':\[([a-zA-Z0-9]+)\]/', '', $_COOKIE['estelife_likes']);
		}else
			$sCookie = '';

		if ($bFlag)
			$sCookie.='id'.$this->elId.$this->type.':['.$sHash.']';

		setcookie('estelife_likes', $sCookie, time() + 12*60*60*24*30, '/');

		return true;
	}

	public function checkCookie($hash=false){
		$obLike=VDatabase::driver();

		//проверка кук
		if (!$hash){
			if ($this->user_id<=0 && isset($_COOKIE['estelife_likes']) && !empty($_COOKIE['estelife_likes'])){
				preg_match('/id'.$this->elId.$this->type.':\[([a-zA-Z0-9]+)\]/', $_COOKIE['estelife_likes'], $matches);
				if ($matches)
					$hash = $matches[1];
			}
		}

		if ($hash){
			//проверка записи в базе
			$obQuery=$obLike->createQuery();
			$obQuery->builder()->from('estelife_user_likes');
			$obQuery->builder()->filter()
				->_eq(
					$obQuery->builder()->_md5('id','like_id','user_id', 'ip', 'type', 'estelifesoltlike'),
					$hash
				);
			$arUserLike = $obQuery->select()->assoc();

		}

		return $arUserLike;
	}


	private function updateLike(array $arUserLike){
		if (empty($arUserLike))
			throw new VException('Пустой массив с лайками пользователя');

		$obLike=VDatabase::driver();
		//получаем запись лайка
		$obQuery=$obLike->createQuery();
		$obQuery->builder()
			->from('estelife_likes')
			->filter()
			->_eq('id', $arUserLike['like_id']);
		$arLike = $obQuery->select()->assoc();

		if (empty($arLike))
			throw new VException('Пустой массив с лайками');

		if ($arUserLike['type'] == $this->typeLike && $this->typeLike == 1)
			$arLike['countLike']--;
		elseif ($arUserLike['type'] == $this->typeLike && $this->typeLike == 2)
			$arLike['countDislike']--;
		elseif ($arUserLike['type'] != $this->typeLike && $this->typeLike == 1){
			$arLike['countLike']++;
			$arLike['countDislike']--;
		}elseif ($arUserLike['type'] != $this->typeLike && $this->typeLike == 2){
			$arLike['countLike']--;
			$arLike['countDislike']++;
		}

		$obQuery=$obLike->createQuery();
		$obQuery->builder()
			->from('estelife_likes')
			->value('countLike', abs($arLike['countLike']))
			->value('countDislike', abs($arLike['countDislike']))
			->filter()
			->_eq('id', $arLike['id']);
		$nLikeId = $obQuery->update();

		if (!$nLikeId)
			throw new VException('Ошибка в обновлении лайка в likes');

		$flag=false;
		$obQuery = $obLike->createQuery();
		$obQuery->builder()
			->from('estelife_user_likes')
			->filter()
			->_eq('id', $arUserLike['id']);


		if ($arUserLike['type'] == $this->typeLike){
			//Удаляем лайк пользователя
			if ($obQuery->delete())
				$flag = true;
			if ($this->user_id<=0)
				$this->setCookie();
		}else{
			//Обновлям лайк пользователя
			$obQuery->builder()->value('type', $this->typeLike);
			if ($obQuery->update())
				$flag = true;

			if ($this->user_id<=0){
				$arLike['md5'] = md5($arUserLike['id'].$arUserLike['like_id'].$this->user_id.$this->ip.$this->typeLike.'estelifesoltlike');
				$this->setCookie($arLike['md5'], true);
			}
		}

		if ($flag==false)
			throw new VException('Ошибка в обновлении лайка пользователя');


		return $arLike;
	}

}