<?php
namespace core\types;

/**
 * Описание класса не задано. Обратитесь с вопросом к разработчику.
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 * @since 30.05.13
 */
class VString {
	private static $arCapitalized=array();

	/**
	 * Изменяет слово так, чтобы первый символ был в верхнем регистре
	 * @param string
	 * @param bool $bCache
	 * @return string
	 */
	public static function capitalize($sName, $bCache=true){
		if($bCache && isset(self::$arCapitalized[$sName]))
			return self::$arCapitalized[$sName];

		if(preg_match('#(_){1,}#',$sName)){
			$arNames=explode('_',$sName);
			$sCapitalized='';
			foreach($arNames as $sValue)
				$sCapitalized.=self::capitalize($sValue,false);
		}else
			$sCapitalized=strtoupper(substr($sName,0,1)).substr($sName,1);

		if($bCache)
			self::$arCapitalized[$sName]=$sCapitalized;

		return $sCapitalized;
	}

	/**
	 * Совершает обратно действие для функции capitalize
	 * @param $sName
	 * @return string
	 */
	public static function deCapitalize($sName){
		$sName=preg_replace('#[A-Z]+#','_$0',$sName);
		return mb_strtolower(mb_substr($sName,1));
	}

	/**
	 * Функция осуществляет транслитерацию кирилицы в латиницу
	 * @param string
	 * @return string
	 */
	public static function translit($sCirilic){
		$arLetters=array('а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'yo','ж'=>'zh','з'=>'z','и'=>'i','й'=>'ji','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h','ц'=>'ts','ч'=>'ch','ш'=>'sh','щ'=>'sch','ь'=>'','ы'=>'y','ъ'=>'','э'=>'e','ю'=>'yu','я'=>'ya');
		return str_replace(
			array_keys($arLetters),
			array_values($arLetters),
			preg_replace(
				array('#[\-_\s]+#','#([^а-яa-z0-9\s\-]+)#iu'),
				array('-',''),
				mb_strtolower(trim($sCirilic),'UTF-8')
			)
		);
	}

	/**
	 * Осуществляет проверку страки на соответствие идентификатору
	 * @param $sTranslit
	 * @return int
	 */
	public static function isIdent($sTranslit){
		return preg_match('/^[a-z0-9_-]+$/',$sTranslit);
	}

	/**
	 * Проверяет корректность email
	 * @param string $sEmail
	 * @return boolean
	 */
	public static function isEmail($sEmail){
		return preg_match('/^[a-z\d](?:[\w\.-]*[a-z\d])*@(?:[a-z\d](?:[a-z\d-]*[a-z\d])*\.)+[a-z]{2,4}$/i',$sEmail);
	}

	/**
	 * Проверяет валидность номера телефона
	 * @param string $sPhone
	 * @return int
	 */
	public static function isPhone($sPhone){
		$sPhone=preg_replace('#([^\d]*)#','',$sPhone);
		return !(empty($sPhone) || strlen($sPhone)<10);
	}

	/**
	 * Проверяет урл
	 * @param $sUrl
	 * @return int
	 */
	public static function isUrl($sUrl){
		return preg_match('#^((https?|ftp)\:\/\/){1}([a-z_\-\.\/]+)(\?[0-9a-z=\/_\-&\%]+)$#i',$sUrl);
	}

	/**
	 * @param $sPhone
	 * @param $flag
	 * @return string
	 */
	public static function formatPhone($sPhone, $flag=false){
		if ($flag == true){
			return (self::isPhone($sPhone)) ?
				preg_replace('#^\+?([0-9]{1})[\(\s]*([0-9]{3})[\)\s]*([0-9]{3})\-?([0-9]{2})\-?([0-9]{2})#','+7 ($2) <b>$3-$4-$5</b>',$sPhone) :
				'';
		}else{
			return (self::isPhone($sPhone)) ?
				preg_replace('#^\+?([0-9]{1})[\(\s]*([0-9]{3})[\)\s]*([0-9]{3})\-?([0-9]{2})\-?([0-9]{2})#','+7 ($2) $3-$4-$5',$sPhone) :
				'';
		}
	}

	/**
	 * Конвертит окончание в зависиомсти от целого числа
	 * @param $val
	 * @param bool $sEnds
	 * @internal param int $nNum
	 * @internal param array $arEnds
	 * @return string
	 */
	public static function spellAmount($val,$sEnds=false){
		$arEnds=array(
			'1'=>'',
			'2-5'=>'а',
			'def'=>'ов'
		);
		if(is_string($sEnds))
		{
			$arEndsTmp=explode(',',$sEnds);
			$arEnds=array(
				'1'=>$arEndsTmp[0],
				'2-5'=>$arEndsTmp[1],
				'def'=>$arEndsTmp[2]
			);
		}
		if($val>1000000) $val=$val%1000000;
		if($val>100000) $val=$val%100000;
		if($val>10000) $val=$val%10000;
		if($val>1000) $val=$val%1000;
		if($val>100) $val=$val%100;
		if($val==0) return $arEnds['def'];
		if($val==1) return $arEnds['1'];
		if($val<20)
		{
			if($val<5) return $arEnds['2-5'];
			else return $arEnds['def'];
		}
		else
		{
			$minor=$val%10;
			if($minor==1) return $arEnds['1'];
			if($minor==0) return $arEnds['def'];
			if($minor<5) return $arEnds['2-5'];
		}
		return $arEnds['def'];
	}

	/**
	 * Укарачивает сторку на определенную длинну
	 * @param string $sText
	 * @param int $nLimit
	 * @param string $sEnd
	 * @param string $sPoint
	 * @return string
	 */
	public static function truncate($sText,$nLimit,$sEnd='...',$sPoint=' '){
		$nLength=strlen($sText);
		if($nLength<=$nLimit)
			return $sText;

		$nPoint=strpos($sText,$sPoint,$nLimit);
		if($nPoint==false || $nPoint>=$nLength)
			return $sText;

		return substr($sText,0,$nPoint).$sEnd;
	}

	/**
	 * Подготавливает объект для сохранения в json
	 * @param mixed
	 * @return mixed
	 */
	public static function jsonFixCyr($mVar){
		if(is_array($mVar)){
			$arTemp=array();
			foreach($mVar as $mKey=>$mValue) {
				$arTemp[self::jsonFixCyr($mKey)] = self::jsonFixCyr($mValue);
			}
			$mVar=$arTemp;
		}elseif(is_object($mVar)){
			$arVars=get_object_vars($mVar);
			foreach ($arVars as $sVar=>$mValue) {
				$mVar->$sVar=self::jsonFixCyr($mValue);
			}
		}elseif(is_string($mVar)){
			$mVar=iconv('cp1251', 'utf-8', $mVar);
		}
		return $mVar;
	}

	/**
	 * Безопасное сохранение данных в json, содержащих кирилицу
	 * @param mixed
	 * @return bool
	 */
	public static function jsonSafeEncode($mVar){
		return json_encode(self::jsonFixCyr($mVar));
	}

	/**
	 * Выбирает не пустую строку из двух переданных. В приоритете первая строка.
	 * @param $sStringOne
	 * @param $sStringTwo
	 */
	public static function ignoreEmpty($sStringOne,$sStringTwo){
		return (!empty($sStringOne)) ?
			$sStringOne :
			$sStringTwo;
	}

	/**
	 * Подправляет ссылку и делаем короткий урл
	 * @param $sUrl
	 * @param bool $bAppendTail
	 * @return array
	 */
	public static function checkUrl(&$sUrl,$bAppendTail=false){
		$sUrl=trim($sUrl);
		$sShort=$sUrl;

		if(preg_match('#^(https?:\/\/)*([^\/]+)(\/.*)*#',$sUrl,$arMatches)){
			$sUrl=((!empty($arMatches[1])) ? $arMatches[1] : 'http://').$arMatches[2].$arMatches[3];
			$sShort=$arMatches[2];
			$nLength=!empty($arMatches[3]) ? strlen($arMatches[3]) : 0;

			if($bAppendTail && $nLength>1)
				$sShort.=($nLength>10) ? substr($arMatches[3],0,10).'...' : $arMatches[3];
		}

		return $sShort;
	}
}