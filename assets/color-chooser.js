$(document).ready(function() {
	//Inserts IDs and classes where needed for farbtastic
	$('.color-chooser:first').before('<div id="picker"></div>');
	$('.color-chooser input').attr({class : "colorwell"});
	//Regular farbtastic statement
	var f = $.farbtastic('#picker');
	var p = $('#picker').css('opacity', 0.25);
	var selected;
	$('.colorwell')
	.each(function () { f.linkTo(this); $(this).css('opacity', 0.75); })
	.focus(function() {
		if (selected) {
			$(selected).css('opacity', 0.75).removeClass('colorwell-selected');
		}
		f.linkTo(this);
		p.css('opacity', 1);
		$(selected = this).css('opacity', 1).addClass('colorwell-selected');
	});
});