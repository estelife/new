<?php

if(count($argv)<3)
{
	echo "Слишком мало параметров\n";
	die(1);
}
$sFromTag=$argv[1];
$sToTag=$argv[2];

$sRootDir=dirname(__FILE__);
$sUpdateFileName='update_'.$sFromTag.'_'.$sToTag;
$archExt='.tar';
$zipExt='.tar.gz';
//Здесь формируем архив обновлений и отсылаем на него ссылку
$query='git diff --name-only '.$sFromTag.'..'.$sToTag;
$sFiles=shell_exec($query);
$arFiles=explode("\n",$sFiles);
$arNewFiles=array();
foreach($arFiles as $key=>$value)
{
	$sName=trim($value);

	if($sName=='' || !@file_exists($sRootDir.'/'.$sName)) 
		continue;
	
	$arNewFiles[]=$sName;
}

$sNewFiles=join(' ',$arNewFiles);
$query='git archive --format=tar '.$sToTag.' '.$sNewFiles.' > '.$sUpdateFileName.$archExt;
shell_exec($query);
if(filesize($sUpdateFileName.$archExt)<100)
{
	@unlink($sUpdateFileName.$archExt);
	echo "Пустое обновление";
	die(2);
}
$query="gzip -9f ".$sUpdateFileName.$archExt;
$res=shell_exec($query);

