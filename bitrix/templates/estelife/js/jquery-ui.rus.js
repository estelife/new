/**
 * Created with JetBrains PhpStorm.
 * User: dmitriy
 * Date: 21.10.13
 * Time: 17:53
 * To change this template use File | Settings | File Templates.
 */
jQuery(function($){
	$.datepicker.regional['ru'] = {
		closeText: 'Закрыть',
		prevText: '&#x3c;Пред',
		nextText: 'След&#x3e;',
		currentText: 'Сегодня',
		monthNames: ['января','февраля','марта','апреля','мая','июня',
			'июля','августа','сентября','октября','ноября','декабря'],
		monthNamesShort: ['янв','фев','мар','апр','май','июн',
			'июл','авг','сен','окт','ноя','дек'],
		dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'],
		dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'],
		dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
		weekHeader: 'Не',
		dateFormat: 'dd MM yy',
		firstDay: 1,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: ''};
	$.datepicker.setDefaults($.datepicker.regional['ru']);
});