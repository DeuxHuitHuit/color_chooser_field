$(document).ready(function() {
	// Find TDs that contain a '#'
	$("tbody tr td:contains('#')").each(function() {
		
		var $td = $(this)
		// Select the value of the TD
		var td_data = $td.text()
		// Strip out any extra spaces
		td_data = jQuery.trim(td_data);
		
		// Validate hex length
		if(td_data.length == 7 || td_data.length == 4){
			// Add a SPAN for the color box
			$td.prepend('<span>\&nbsp;</span>');	
			// Add styles to the SPAN 
			$td.children('span:first-child').css({
				'background-color': td_data, 
				'margin-right' : '5px',
				'border' : 'solid 1px #eaeaea',
				'padding' : '3px 8px'
			});

		}
			
	});

});