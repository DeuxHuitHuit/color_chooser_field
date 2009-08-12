(function($) { //Use jQuery comptibility mode

	$(document).ready(function() {
	/*----Index page swatches----*/
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
	
	/*----Color chooser field----*/
		//If page has a color chooser field, call the Farbtastic function
		if($('.color-chooser').length > 0){
		
			//Insert elements for Farbtastic to work with 
			$('.color-chooser input').each (function() {
				$(this).wrap('<div class="color-chooser-container"></div>');
				$(this).before('<div class="color-icon" href="#"> </div>');
				$(this).addClass('colorwell');
			});
			//Insert and hide the color chooser 	
			$('.colorwell:first').after('<div id="picker-container" style="display:none;"><div id="picker-top"></div><div id="picker"></div></div>');
			// Make the chooser appear under the field in focus
			$('.colorwell').each(function () {
				//Call Farbtastic function
				$.farbtastic('#picker').linkTo(this); $(this); 
			})
				.focus(function() {
					//$('#picker').stop();
					$(this).addClass('colorwell-selected');
					$.farbtastic('#picker').linkTo(this);
					$('#picker-container')
						.insertAfter('.colorwell-selected')
						//.removeAttr('style')
						.stop(true, true)
						.fadeIn('300');
				})
				.blur(function() {
					$('#picker-container').fadeOut('300');
					$(this).removeClass('colorwell-selected');
					//$('#picker').attr({'style': 'display:none'});
				});		
		}
	});

})(jQuery);