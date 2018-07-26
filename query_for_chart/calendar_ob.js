(function ($) {
		
  Drupal.behaviors.query_for_chart = {
    attach: function (context){
    	$('#edit-date-begin', context).datepicker({
    		  dateFormat: 'dd.mm.yy',
    	      startDate: '01-01-2008',
    	      lang: "ru",
    	      changeMonth: true,
    	      changeYear: true,
    	      yearRange: "2008:2015",    	        			
  			  monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь', 
  			  'Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'], 
  			  monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн', 
  			  'Июл','Авг','Сен','Окт','Ноя','Дек'], 
  			  dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'], 
  			  dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'], 
  			  dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],   			 
  			  firstDay: 1, 
  			  isRTL: false 
    	});
    		 
    	$('#edit-date-end', context).datepicker({
    		  dateFormat: 'dd.mm.yy',
    		  endDate: '31-12-2015',
    		  lang: "ru",
    		  changeMonth: true,
    	      changeYear: true,
    	      yearRange: "2008:2015",
    	      monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь', 
    	      'Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'], 
    	      monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн', 
    	      'Июл','Авг','Сен','Окт','Ноя','Дек'], 
    	      dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'], 
    	      dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'], 
    	      dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],   			 
    	      firstDay: 1, 
    	      isRTL: false
    	});
    }	
  }
}) (jQuery);