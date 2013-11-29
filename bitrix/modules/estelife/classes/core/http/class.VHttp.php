<?php
namespace core\http;
use core\exceptions\VException;

/**
 * Описание класса не задано. Обратитесь на email автора с вопросом, указав имя класса.
 * @author Dmitriy Konev <dnkonev@yandex.ru>
 * @since 12.03.13
 */
class VHttp {
	private $obListener;
	protected $sHost;
	protected $arOptions;

	const GET=1;
	const POST=2;

	public function __construct($sUrl){
		if(!preg_match('#^((https|http):\/\/(.*))$#i',$sUrl,$arMatches))
			throw new VException('wv_incorrect_url');

		$sHost=$arMatches[3];
		$arTemp=explode('/',$sHost);
		$this->sHost=$arTemp[0];
		unset($arTemp[0]);

		$this->arOptions=array(
			'method'=>self::GET,
			'path'=>'/'.((!empty($arTemp)) ? implode('/',$arTemp) : ''),
			'ssl'=>($arMatches[2]=='https'),
			'content_type'=>'application/x-www-form-urlencoded',
			'timeout'=>120
		);

		$this->arOptions['port']=($this->arOptions['ssl']) ?
			443 :
			80;
	}

	public function connect(){
		$this->obListener=@fsockopen(
			(($this->arOptions['ssl'])?'ssl':'tcp').'://'.$this->sHost,
			$this->arOptions['port'],
			$errno,
			$errstr,
			$this->arOptions['timeout']
		);

		if(!$this->obListener)
			throw new VException($errstr,$errno);
	}

	public function setMethod($nMethod){
		if($nMethod!=self::GET && $nMethod!=self::POST)
			throw new VException('wv_incorrect_http_method');

		$this->arOptions['method']=$nMethod;
	}

	public function setPath($sPath){
		$this->arOptions['path']=$sPath;
	}

	public function setContentType($sContentType){
		$this->arOptions['content_type']=$sContentType;
	}

	public function query($mData=null,$bNotEncode=false){
		$this->connect();

		$arPath=explode('?',$this->arOptions['path']);
		$sPath=$arPath[0];

		$mData=$this->prepareParams(
			$mData,
			((isset($arPath[1])) ? $arPath[1] : ''),
			$bNotEncode
		);

		if(!empty($mData)){
			$arParams=array();

			foreach($mData as $mKey=>$sValue){
				if(is_numeric($mKey))
					$arParams[]=$sValue;
				else
					$arParams[]=$mKey.'='.$sValue;
			}

			$sParams=implode('&',$arParams);
		}else{
			$sPath='';
		}

		$sQuery=(($this->arOptions['method']==self::GET) ? 'GET' : 'POST')." ".$sPath.(($this->arOptions['method']==self::GET && !empty($sParams)) ? '?'.$sParams : '')." HTTP/1.0\r\n";
		$sQuery.="Host: ".$this->sHost."\r\n";
		$sQuery.="Referer: ".$this->sHost."\r\n";
		$sQuery.="Pragma: no-cache\r\n";
		$sQuery.="User-Agent:Mozilla/5.0 (X11; Linux i686) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.56 Safari/537.17\r\n";
		$sQuery.="Accept-Charset:windows-1251,utf-8\r\n";
		$sQuery.="Accept:text/html,text/xml,application/xml,application/json,text/json\r\n";
		$sQuery.="Content-Type:".$this->arOptions['content_type']."\r\n";

		if($this->arOptions['method']==self::POST)
			$sQuery.="Content-Length: ".strlen($sParams)."\r\n";

		$sQuery.="Connection: Close\r\n\r\n";

		if($this->arOptions['method']==self::POST && !empty($sParams))
			$sQuery.=$sParams."\r\n\r\n";

		fwrite($this->obListener,$sQuery);
	}

	public function queryFormData($mData,$arFiles){
		$this->connect();

		$arPath=explode('?',$this->arOptions['path']);
		$sPath=$arPath[0];

		$arParams=$this->prepareParams(
			$mData,
			((isset($arPath[1])) ? $arPath[1] : '')
		);

		$sBoundary=md5(uniqid(time()));

		$sQuery="POST ".$sPath." HTTP/1.0\r\n";
		$sQuery.="Host: ".$this->sHost."\r\n";
		$sQuery.="Referer: ".$this->sHost."\r\n";
		$sQuery.="Pragma: no-cache\r\n";
		$sQuery.="User-Agent:Mozilla/5.0 (X11; Linux i686) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.56 Safari/537.17\r\n";
		$sQuery.="Accept-Charset:utf-8\r\n";
		$sQuery.="Accept:text/html,text/xml,application/xml,application/json,text/json\r\n";
		$sQuery.="Content-Type:multipart/form-data; boundary=".$sBoundary."\r\n";

		$sContent='';

		if(!empty($arFiles)){
			foreach($arFiles as $sKey=>$sFile){
				if(!file_exists($sFile))
					continue;

				$sContent.="--".$sBoundary."\r\n";
				$sContent.="Content-Disposition: form-data; name=\"".$sKey."\"; filename=\"".basename($sFile)."\"\r\n";
				$sContent.="Content-Type: image/jpeg\r\n";
				$sContent.="Content-Transfer-Encoding: binary\r\n\r\n";
				$sContent.=file_get_contents($sFile)."\r\n";
			}
		}

		if(!empty($arParams)){
			foreach($arParams as $sKey=>$sValue){
				$sContent.="--".$sBoundary."\r\n";
				$sContent.="Content-Disposition: form-data; name=\"".$sKey."\"\r\n\r\n";
				$sContent.=$sValue."\r\n";
			}
		}

		$sQuery.="Content-Length: ".strlen($sContent)."\r\n\r\n";
		//$sQuery.="Connection: Close\r\n\r\n";
		$sQuery.=$sContent;
		$sQuery.="--".$sBoundary."--\r\n\r\n";

		fwrite($this->obListener,$sQuery);
	}

	public function read(){
		$sResponse='';
		$arResult=array();

		while(!feof($this->obListener)){
			$sResponse.=fread($this->obListener,10);
		}

		$this->close();

		if(!empty($sResponse)){
			$arResponse=explode("\r\n\r\n",$sResponse);
			$arResult['headers']=$arResponse[0];
			$arResult['body']=$arResponse[1];
		}

		return $arResult;
	}

	public function close(){
		fclose($this->obListener);
	}

	private function prepareParams($mData,$sQueryString,$bNotEncode=false){
		if(!is_array($mData)){
			$arTemp=explode('&',$mData);
			$mData=array();

			foreach($arTemp as $mValue){
				$mValue=explode('=',$mValue);

				if(isset($mValue[1]))
					$mData[$mValue[0]]=$mValue[1];
				else
					$mData[]=$mValue[0];
			}
		}

		if(!empty($sQueryString)){
			$arTemp=explode('&',$sQueryString);

			foreach($arTemp as $mValue){
				$mValue=explode('=',$mValue);

				if(isset($mValue[1]) && !isset($mData[$mValue[0]]))
					$mData[$mValue[0]]=$mValue[1];
			}
		}

		if(!empty($mData)){
			foreach($mData as $mKey=>&$sValue){
				if(!$bNotEncode)
					$sValue=urlencode($sValue);
			}
		}

		return $mData;
	}
}