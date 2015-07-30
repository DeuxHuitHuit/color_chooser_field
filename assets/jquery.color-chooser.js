(function($) {
	
	// patch jquery for tooltip
	$.browser = $.browser || {};
	
	$(document).ready(function() {
		/*----Index page swatches----*/
		// Find TDs that contain a '#'
		$("tbody tr td.field-colorchooser:contains('#')").each(function() {

			var $td = $(this)
			// Select the value of the TD
			var td_data = $td.text()
			// Strip out any extra spaces
			td_data = jQuery.trim(td_data);

			// Validate hex length
			if(td_data.length == 7 || td_data.length == 4){
				// Remove readable hex-value
				$td.html('');
				// Add a span-element that's formatted as color preview
				// Attach hex-value as title
				$td.prepend('<span class="colorchooser-index-preview" title="'+td_data+'" style="background-color: '+td_data+';">\&nbsp;</span>');
			}

		});

		/*----Color chooser field----*/
		//If page has a color chooser field, call the Farbtastic function
		if($('label.color-chooser').length > 0){
			//Insert elements for Farbtastic to work with 
			$('.color-chooser input').each (function() {
				$(this).wrap('<div class="color-chooser-container"></div>');
				$(this).before('<div class="color-icon" href="#"> </div>');
				$(this).addClass('colorwell');
			});
			//Insert and hide the color chooser 	
			$('body').append('<div id="picker-container" style="display:none;"><div id="picker-top"></div><div id="picker"></div><div id="picker-bottom"></div></div>');
			// Apply Farbtastic to color fields
			$('.colorwell').each(function () {
				$.farbtastic('#picker').linkTo(this); 
			})
			// Link Farbtastic to the field in focus
			.focus(function() {
				$.farbtastic('#picker').linkTo(this);
			});    
			//jQuery Tools - Tooltip
			$('.colorwell').tooltip({ 
		    	effect: 	'fade',
		    	position: 	'bottom left',
		    	offset: 	[0, 166], 
		    	tip: 		'#picker-container'
	    		}).dynamic( { 
					top: { 
	        		direction: 	'up', 
	    		} 
	    	});
		}
	});
})(jQuery);