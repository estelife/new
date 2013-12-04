<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<div class="social cols">
	<div class="cols">
		<!-- Put this script tag to the <head> of your page -->
		<script type="text/javascript" src="//vk.com/js/api/openapi.js?100"></script>

		<script type="text/javascript">
		  VK.init({apiId: 3865164, onlyWidgets: true});
		</script>

		<!-- Put this div tag to the place, where the Like block will be -->
		<div id="vk_like"></div>
		<script type="text/javascript">
		VK.Widgets.Like("vk_like", {type: "mini"});
		</script>
	</div>
	<div class="cols">
		<iframe src="//www.facebook.com/plugins/like.php?href=<?=urlencode("http://estelife.ru".$_SERVER['REQUEST_URI'])?>&amp;width=150&amp;height=21&amp;colorscheme=light&amp;layout=button_count&amp;action=like&amp;show_faces=false&amp;send=false&amp;appId=125608317483440" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:150px; height:21px;" allowTransparency="true"></iframe>
	</div>
	<div class="cols">
		<a href="https://twitter.com/share" class="twitter-share-button" data-lang="ru">Твитнуть</a>
		<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
	</div>
	<div class="cols">
		<!-- Place this tag where you want the +1 button to render. -->
		<div class="g-plusone" data-size="medium"></div>

		<!-- Place this tag after the last +1 button tag. -->
		<script type="text/javascript">
		  window.___gcfg = {lang: 'ru'};

		  (function() {
			var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
			po.src = 'https://apis.google.com/js/plusone.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
		  })();
		</script>
	</div>
</div>
