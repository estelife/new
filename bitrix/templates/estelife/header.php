<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=1024" />
	<meta name="format-detection" content="telephone=no">
	<title><?$APPLICATION->ShowTitle()?></title>
	<?$APPLICATION->ShowMeta("robots")?>
	<?$APPLICATION->ShowMeta("keywords")?>
	<?$APPLICATION->ShowMeta("description")?>
	<script type="text/javascript" src="/bitrix/templates/estelife/js/libraries.js?<?=filemtime($_SERVER['DOCUMENT_ROOT']."/bitrix/templates/estelife/js/libraries.js")?>"></script>
	<script type="text/javascript" src="/bitrix/templates/estelife/js/estelife.js"></script>
	<script type="text/javascript" data-main="/bitrix/templates/estelife/js/ajax" src="/bitrix/templates/estelife/js/require.js"></script>
	<script type="text/javascript" src="/bitrix/js/estelife/vMapStyle.js"></script>
	<script type="text/javascript" src="/bitrix/js/estelife/vMap.js"></script>
	<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyAZfcZn-KLKm52_chZk22TGMdooeDvMYfI&sensor=false"></script>
	<script type="text/javascript" src="https://www.youtube.com/player_api"></script>
	<link rel="icon" href="/favicon.png" type="image/icon">
	<link rel="shortcut icon" href="/favicon.png" type="image/icon">
	<link rel="stylesheet" type="text/css" href="/bitrix/templates/estelife/css/style.css" />
	<link rel="stylesheet" type="text/css" href="/bitrix/templates/estelife/template_styles.css" />
	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	  ga('create', 'UA-43968108-1', 'estelife.ru');
	  ga('send', 'pageview');
	</script>
	<?$APPLICATION->ShowHeadStrings()?>
</head>
<body>
<div class="wrap">
	<div class="wrap-fix">
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
					<a href="http://vk.com/club58189724" class="vk" target="_blank">ВКонтакте</a>
					<a href="https://www.facebook.com/EsteLife.RU" class="fb" target="_blank">Facebook</a>
					<a href="https://plus.google.com/u/0/107415582900361267191/posts" class="gp" target="_blank">Google+</a>
					<a href="https://twitter.com/estelife" class="tw" target="_blank">Twitter</a>
					<span>Наша новостная лента:</span>
					<a href="http://feeds.feedburner.com/estelife/SgfF" target="_blank" class="rss">RSS</a>
				</div>
				<?php
				$APPLICATION->IncludeComponent(
					"estelife:auth.top",
					"",
					array(),
					false
				);
				?>
			</div>
		</div>
		<div class="cities main_cities none"></div>
		<div class="head">
			<a href="/" class="logo">
				Портал<br /> эстетической медицины
			</a>
			<div class="buket">
				<img src="/bitrix/templates/estelife/images/buket.png" >
			</div>
			<? $APPLICATION->IncludeComponent("estelife:search.form", "", Array(
					"PAGE"	=>	"/search/"
				)
			);?>
			<?$APPLICATION->IncludeComponent('estelife:menu.estelife', '', array())?>
		</div>
		<div class="wrap-content">