<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetPageProperty("description", "Практический вебинар по ботулинотерапии");
$APPLICATION->SetPageProperty("keywords", "Практический вебинар по ботулинотерапии");
$APPLICATION->SetPageProperty("title", "Практический вебинар по ботулинотерапии");
?>
<div class="content">
		<ul class="crumb">
			<li><a href="/">Главная</a></li>
			<li><a href="/about/">О проекте</a></li>
			<li><b>Практический вебинар по ботулинотерапии</b></li>
		</ul>
		<div class="item detail education">
			<div class="top">
				<img src="/bitrix/templates/estelife/images/education/moosbt.png" />
				<div class="about">
					<h1>Практический вебинар по ботулинотерапии</h1>
					<p>Совместный информационно-образовательный проект МООСБТ и «Меддиз»</p>
					<ul>
						<li>Формат:  Вебинар</li>
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
					<b>Тема вебинара</b>
					<p>«Блефароспазм; гемифациальный спазм и другие заболевания лицевого нерва. Клиника, диагностика, лечение локальными инъекциями ботулотоксина»  </p>
				</div>
			</div>
			<div class="description">
				<p>Уважаемые коллеги!</p>
				<p>Мы рады пригласить вас на вебинар по ботулинотерапии Президента МООСБТ доктора медицинских наук,<br /> профессора О.Р. Орловой и «Клиники доктора Груздева» (Санкт-Петербург) для неврологов, косметологов,<br /> офтальмологов и врачей смежных специальностей.</p>
				<p>В ходе прямого эфира будут наглядно представлены клинические случаи пациентов и техника процедуры<br /> инъекции БТА.</p>
			</div>
			<?$APPLICATION->IncludeComponent("estelife:subscribe.bitrix",
				"education",
				Array()
			);?>
		</div>
</div>
<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");