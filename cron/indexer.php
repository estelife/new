<?php
try{
	if(empty($_SERVER['argv']))
		throw new Exception('execute this script from shell');

	if($_SERVER['argc']!=3)
		throw new Exception('use as: php -f indexer.php <name of config file> <name of index>');

	$sFileName=$_SERVER['argv'][1];
	$sIndexName=$_SERVER['argv'][2];

	$sPath=__DIR__;
	$nLevel=0;
	$bFound=false;

	while($nLevel<3){
		$sFile=$sPath.'/'.$sFileName;

		if($bFound=is_file($sFile))
			break;

		$sPath=realpath($sPath.'/../');
		$nLevel++;
	}

	if(!$bFound)
		throw new Exception('sphinx config file not found');

	$sContent=file_get_contents($sFile);

	if(!preg_match('#[\s]+index[\s]+'.$sIndexName.'#',$sContent))
		throw new Exception('index ['.$sIndexName.'] not found in config file ['.$sFile.']');

	shell_exec('searchd -c '.$sFile.' --stop');

	$sLog=date('d.m.Y H:i:s')."\r\n";
	$sLog.=shell_exec('indexer --rotate -c '.$sFile.' '.$sIndexName);
	$sLog.=shell_exec('indexer --merge estelife '.$sIndexName.' -c '.$sFile);

	shell_exec('searchd -c '.$sFile);

	file_put_contents(__DIR__.'/indexer.log',$sLog."\r\n---------------------------\r\n\r\n");
	echo $sLog;
}catch(Exception $e){
	$sLog=date('d.m.Y H:i:s')."\r\n";
	$sLog.=$e->getMessage()."\r\n";
	file_put_contents(__DIR__.'/indexer.log',$sLog."\r\n---------------------------\r\n\r\n");
}