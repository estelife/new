<div class="item detail education">
	<?php if($arResult['allow']): ?>
		<div class="title">
			<h1>Практический телемост по ботулинотерапии</h1>
		</div>
		<div id='player'></div>
		<script type='text/javascript' src='http://www.aloha.cdnvideo.ru/aloha/jwplayer/js_for_embed/jwplayer.js'></script>
		<script type='text/javascript'>
			jwplayer('player').setup({
				'id': 'player',
				'width': '480',
				'height': '320',
				'provider': 'rtmp',
				'streamer': 'rtmp://aloha.cdnvideo.ru/aloha',
				'file': 'meddis.sdp',
				'src': 'http://www.aloha.cdnvideo.ru/aloha/jwplayer/mediaplayer-viral/player-viral.swf',
				'autoplay': 'false',
				'modes': [
					{type: 'flash', src: 'http://www.aloha.cdnvideo.ru/aloha/jwplayer/mediaplayer-viral/player-viral.swf'},
					{
						type: 'html5',
						config: {
							'file': 'http://aloha.cdnvideo.ru/aloha/meddis.sdp/playlist.m3u8',
							'provider': 'video'
						}
					}
				]
			});
		</script>
	<?php else: ?>
		<div class="top">
			<img src="/bitrix/templates/estelife/images/education/moosbt.png" />
			<div class="about">
				<h1>Практический телемост по ботулинотерапии</h1>
				<p>Совместный информационно-образовательный проект МООСБТ и «Меддиз»</p>
				<ul>
					<li>Формат:  Телемост</li>
					<li>Дата проведения: 19.02.2014, 10:00 МСК</li>
				</ul>
			</div>
			<img src="/bitrix/templates/estelife/images/education/meddiz.png" />
		</div>
		<div class="theme">
			<div class="user">
				<div class="img">
					<img src="/bitrix/templates/estelife/images/education/lektor.png" />
				</div>
				<div class="about">
					<b>Лектор</b>
					<h3>Орлова Ольга Ратмировна</h3>
					<i>Президент МООСБТ,<br />
						доктор медицинских наук,<br />
						профессор</i>
				</div>
			</div>
			<div class="text">
				<b>Тема телемоста</b>
				<p>«Блефароспазм; гемифациальный спазм и другие заболевания лицевого нерва. Клиника, диагностика, лечение локальными инъекциями ботулотоксина»  </p>
			</div>
		</div>
		<div class="description">
			<p>Уважаемые коллеги!</p>
			<p>Мы рады пригласить вас на телемост по ботулинотерапии Президента МООСБТ доктора медицинских наук,<br /> профессора О.Р. Орловой и «Клиники доктора Груздева» (Санкт-Петербург) для неврологов, косметологов,<br /> офтальмологов и врачей смежных специальностей.</p>
			<p>В ходе прямого эфира будут наглядно представлены клинические случаи пациентов и техника процедуры<br /> инъекции БТА.</p>
		</div>
		<?$APPLICATION->IncludeComponent("estelife:education.pay",
			"",
			Array()
		);?>
	<?php endif; ?>
</div>