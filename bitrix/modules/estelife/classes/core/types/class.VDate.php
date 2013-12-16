<?php
namespace core\types;

/**
 * Описание класса не задано. Обратитесь с вопросом к разработчику.
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 * @since 30.05.13
 */
class VDate {
	/**
	 * Выполняет преобразование даты из timestamp в строковый вариант
	 * @param $nTime
	 * @param bool $bHourInclude
	 * @return string
	 */
	public static function convertDate($nTime,$bHourInclude=false){
		$arDate=explode('.',date('d.m.Y.H.i',$nTime));
		$arMonths=array(
			'января','февраля','марта','апреля','мая','июня','июля','августа','сентября','октября','ноября','декабря'
		);
		return $arDate[0].' '.$arMonths[$arDate[1]-1].' '.$arDate[2].(($bHourInclude) ? ' в '.$arDate[3].':'.$arDate[4] : '');
	}

	/**
	 * Вывод даты с названием месяца в зависимости от языка
	 * @param bool $nTime
	 * @param string $sFormat
	 * @param string $sLang
	 * @internal param $int верся в timestamp
	 * @return string
	 */
	public static function date($nTime=false,$sFormat='j F Y',$sLang='ru'){
		if(!$nTime)
			$nTime=time();

		$sDate=date($sFormat,$nTime);

		if($sLang=='ru'){
			$arEnMonths=array('January','February','March','April', 'May', 'June','July','August','September','October','November','December');
			$arRuMonths=array('января','февраля','марта','апреля','мая','июня','июля','августа','сентября','октября','ноября','декабря');

			$sDate=str_replace($arEnMonths,$arRuMonths,$sDate);
		}

		return $sDate;
	}

	/**
	 * Возвращает значение timestamp из даты, сгенерированной предыдущим методом
	 * @param $sDate
	 * @param string $sLang
	 * @internal param $string дата
	 * @return int
	 */
	public static function dateToTime($sDate, $sLang='ru'){
		if($sLang=='ru'){
			$sDate=mb_strtolower($sDate,'utf-8');

			$arEnMonths=array('january','february','march','april','may','june','july','august','september','october','november','december');
			$arRuMonths=array('января','февраля','марта','апреля','мая','июня','июля','августа','сентября','октября','ноября','декабря');
			$sDate=str_replace($arRuMonths,$arEnMonths,$sDate);

			$arRuMonths=array('январь','февраль','март','апрель','май','июнь','июль','август','сентябрь','октябрь','ноябрь','декабрь');
			$sDate=str_replace($arRuMonths,$arEnMonths,$sDate);

		}

		return strtotime($sDate);
	}

	/**
	 * Генерит диапазон дат
	 * @param $arDates
	 * @param $fCallback
	 * @return array
	 */
	public static function createDiapasons(array $arDates,$fCallback=null){
		sort($arDates,SORT_NUMERIC);
		$arTemp=array();
		$nLast=0;
		$nTo=0;
		$nFrom=reset($arDates);
		$nCount=count($arDates)-1;

		foreach($arDates as $nKey=>$nDate){

			$nDate=strtotime(date('d.m.Y 00:00',$nDate));
			$bNotNext=false;

			if(isset($arDates[$nKey-1]))
				$nLast=strtotime(date('d.m.Y 00:00',$arDates[$nKey-1]));

			if($nLast==0 || ($bNotNext=($nDate>($nLast+86400))) || $nCount==$nKey){
				if($nLast>0 || $nCount==$nKey){
					if($nLast>0 && !$bNotNext)
						$nTo=$nDate;
					else if($bNotNext && $nFrom==$nLast)
						$nFrom=$nDate;

					$result=true;

					if(($fCallback && is_callable($fCallback)))
						$result = $fCallback($nFrom,$nTo);

					$arTemp[]=array(
						'from'=>$nFrom,
						'to'=>$nTo,
					);
					$nTo=0;

					if($result === false){
						break;
					}
				}

				$nFrom=$nDate;
			}else if($nDate>=$nLast && $nDate<=($nLast+86400)){
				$nTo=$nDate;
			}
		}
		$arDates=$arTemp;
		return $arDates;
	}

	public static function createDiapasons2(array $arDates,$fCallback=null){
		sort($arDates,SORT_NUMERIC);
		$arTemp=array();
		$nFrom=0;
		$nPrev=0;
		$nCount=count($arDates)-1;

		foreach($arDates as $nKey=>$nDate){
			$nDate=strtotime(date('d.m.Y 00:00',$nDate));

			if(($nPrev>0 && $nDate>$nPrev+86400) || $nKey==$nCount){
				$nFrom=($nFrom>0) ? $nFrom : $nDate;
				$nTo=($nFrom!=$nPrev) ? $nDate : 0;

				$result=true;

				if(($fCallback && is_callable($fCallback)))
					$result = $fCallback($nFrom,$nTo);

				$arTemp[]=array(
					'from'=>$nFrom,
					'to'=>$nTo,
				);

				if(!$result)
					break;

				$nFrom=0;
				$nTo=0;
				$nPrev=0;
			}else{
				$nPrev=$nDate;

				if($nFrom==0)
					$nFrom=$nDate;
			}
		}

		$arDates=$arTemp;
		return $arDates;
	}
}