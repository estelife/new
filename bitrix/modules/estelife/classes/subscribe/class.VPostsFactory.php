<?php
namespace subscribe;

/**
 *
 * @author Maxim Shlemarev <shlemarev@gmail.com>
 * @since 30.01.14
 */


class VPostsFactory {

	public function __construct(VUser $vUser){
		$this->obUser = $vUser;
	}

	//Выводит все акции из общей подписки
	public function getComplex($arUser){


	}

	//Выводит все акции из индивидуальной подписки
	public function getTarget($arUser){



	}

	//Выводит все акции из всевозможных подписок для текущего пользователя
	public  function  getAll($arUser){
		$nUserId = $arUser['id'];
		$nDateLastSend = $arUser['date_last_send'];

		$obData = \core\database\VDatabase::driver();

		$obQuery = $obData->createQuery();
		$obQuery->builder()
			->from('estelife_subscribe_events')
			->filter()
			->_eq('type', 3)
			->_eq('total', 0)
			->_eq('event_active',1)
			->_eq('subscribe_user_id',$nUserId);

		$arData = $obQuery->select()->assoc();

		$arResult = array();

		$nDateSend = $arData['date_send'];

		$nDateSendTime = date('Y-m-d H:i:s',$nDateSend);
		$nCurrentTime = date('Y-m-d H:i:s');


		$obQueryPosts = $obData->createQuery();

		$obQueryPosts->builder()
			->from('iblock_element')
			->filter()
			->_eq('IBLOCK_ID', 14)
			->_eq('ACTIVE', 'Y')
			->_gte('DATE_CREATE', $nDateSendTime);
			//->_gte('ACTIVE_TO', $nCurrentTime);

		$obQueryPosts->builder()
			->field('NAME','name')
			->field('ID','id');

		$arPosts = $obQueryPosts->select()->all();

		VPostsFactory::updateDates('all',$nUserId);

		return $arPosts;
	}


	public function updateDates($index,$nUserId){

		$obData = \core\database\VDatabase::driver();
		$obQuery = $obData->createQuery();

		if($index == 'all'){
			$arEvents = VUser::getAllPostEvents($nUserId);

			foreach($arEvents as $arEvent){

				$obQuery->builder()->from('estelife_subscribe_events')
					->value('date_send', time());

				$obQuery->builder()->filter()
					->_eq('id',$arEvent['id']);
				$obQuery->update();
			}
		}

	}


}