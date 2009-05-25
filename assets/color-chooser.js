$(document).ready(function() {
	//Inserts IDs and classes where needed for farbtastic
	$('.color-chooser:first').before('<div id="picker"></div>');
	$('.color-chooser input').attr({class : "colorwell"});
	//Regular farbtastic statement
	var f = $.farbtastic('#picker');
	var p = $('#picker').css('opacity', 0.25);
	var selected;
	$('.colorwell')
	.each(function () { f.linkTo(this); $(this); })
	.focus(function() {
		if (selected) {
			$(selected).removeClass('colorwell-selected');
		}
		f.linkTo(this);
		p.css('opacity', 1);
		$(selected = this).addClass('colorwell-selected');
	});
});