(function ($) {
		
  Drupal.behaviors.selecting_interval = {
    attach: function (context){
    	$('#edit-date-day-begin', context).datepicker({
    		  dateFormat: 'dd-mm-yy',
    	      startDate: '01-10-2008',
    	      lang: "en",
    	      changeMonth: true,
    	      changeYear: true,
    	      yearRange: "2008:2015",    	        			
    	      monthNames: ['January','February','March','April','May','June','July','August','September','October','November','December'], 
  			  monthNamesShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'], 
  			  dayNames: ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'], 
  			  dayNamesShort: ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'], 
  			  dayNamesMin: ['Su','Mo','Tu','We','Th','Fr','Sa'],   			 
  			  firstDay: 1, 
  			  isRTL: false 
    	});
    		 
    	$('#edit-date-day-end', context).datepicker({
    		  dateFormat: 'dd-mm-yy',
    		  endDate: '31-12-2015',
    		  lang: "en",
    		  changeMonth: true,
    	      changeYear: true,
    	      yearRange: "2008:2015",
    	      monthNames: ['January','February','March','April','May','June','July','August','September','October','November','December'], 
  			  monthNamesShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'], 
  			  dayNames: ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'], 
  			  dayNamesShort: ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'], 
  			  dayNamesMin: ['Su','Mo','Tu','We','Th','Fr','Sa'],   			 
    	      firstDay: 1, 
    	      isRTL: false
    	});
    }	
  }
}) (jQuery);