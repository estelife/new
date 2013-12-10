<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<?$APPLICATION->ShowMeta("robots")?>
	<?$APPLICATION->ShowMeta("keywords")?>
	<?$APPLICATION->ShowMeta("description")?>
	<title><?$APPLICATION->ShowTitle()?></title>
	<script type='text/javascript' src='/bitrix/templates/estelife/js/jquery-1.10.2.min.js'></script>
	<script type='text/javascript' src='/bitrix/templates/estelife/js/history.js'></script>
	<?$APPLICATION->ShowCSS();?>
	<?$APPLICATION->ShowHeadStrings()?>
	<?$APPLICATION->ShowHeadScripts()?>
	<script type='text/javascript' src='/bitrix/templates/estelife/js/estelife.js'></script>
	<script type='text/javascript' src='/bitrix/templates/estelife/app.js?v=<?=filemtime($_SERVER['DOCUMENT_ROOT']."/bitrix/templates/estelife/app.js")?>'></script>
	<script type='text/javascript' src='/bitrix/templates/estelife/js/jquery.mousewheel.js'></script>
	<script type='text/javascript' src='/bitrix/templates/estelife/js/mwheelIntent.js'></script>
	<script type='text/javascript' src='/bitrix/templates/estelife/js/jquery.jscrollpane.js'></script>
	<script type='text/javascript' src='/bitrix/templates/estelife/js/jquery-ui-1.10.3.custom.min.js'></script>
	<script type='text/javascript' src='/bitrix/templates/estelife/js/jquery-ui.rus.js'></script>
	<script type="text/javascript" src="/bitrix/js/estelife/vMapStyle.js"></script>
	<script type="text/javascript" src="/bitrix/js/estelife/vMap.js"></script>
	<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyAZfcZn-KLKm52_chZk22TGMdooeDvMYfI&sensor=false"></script>
	
	<link rel="icon" href="/favicon.ico" type="image/icon">
	<link rel="shortcut icon" href="/favicon.ico" type="image/icon">
	<link rel="stylesheet" type="text/css" href="/bitrix/templates/estelife/css/style.css" />
	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	  ga('create', 'UA-43968108-1', 'estelife.ru');
	  ga('send', 'pageview');

	</script>
</head>
<body>
<? $APPLICATION->ShowPanel();?>
<div class="wrap">
	<div class="panel">
		<div class="panel_in">
			<?php
			$APPLICATION->IncludeComponent(
				"estelife:user.geo",
				"",
				array(),
				false
			);
			?>
			<div class="cols col2 social">
				<span>Мы в соцсетях:</span>
				<a href="http://vk.com/estelife_ru" class="vk">ВКонтакте</a>
				<a href="https://www.facebook.com/EsteLife.RU" class="fb">Facebook</a>
				<a href="#" class="tw">Twitter</a>
				<a href="#" class="lj">Live Journal</a>
				<a href="#" class="ok">Одноклассники</a>
			</div>
			<a href="#" class="cols">Войти</a>
		</div>
	</div>
	<div class="cities main_cities none"></div>
	<div class="head">
		<a href="/" class="logo">
			Портал<br /> эстетической медицины
		</a>
		<? $APPLICATION->IncludeComponent("bitrix:search.form", "flat", Array(
				"PAGE"	=>	"/search/"
			)
		);?>
		<?$APPLICATION->IncludeComponent('estelife:menu.estelife', '', array())?>
	</div>