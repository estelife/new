<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Знак качества Estelife");
$APPLICATION->SetPageProperty("description", "Знак качества Estelife");
$APPLICATION->SetPageProperty("keywords", "Знак качества Estelife");
?>
	<div class="content">
		<ul class="crumb">
			<li><a href="/">Главная</a></li>
			<li><a href="/about/">О проекте</a></li>
			<li><b>Знак качества Estelife</b></li>
		</ul>
		<div class="quality">
			<h1>Знак качества Estelife</h1>
			<img src="/bitrix/templates/estelife/images/icons/quality/quality.png" alt="Знак качества Estelife" title="Знак качества Estelife" />
			<p class="first"><b>Знак качества EsteLife</b> — подтверждение полноты и актуальности опубликованных сведений.</p>
			<p>
				Этим знаком  мы отмечаем те организации, которые ведут с нами тесное сотрудничество.<br />
				Мы предоставляем нашим пользователям только достоверную информацию об оказываемых услугах,<br />
				проводимых мероприятиях, специалистах.
			</p>
		</div>
		<h2 class="show-quality-form active"><a href="#">Заявка на получение знака качества<i></i></a></h2>
		<?php $APPLICATION->IncludeComponent(
			'estelife:clinics.request',
			'',array()
		)?>
	</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>