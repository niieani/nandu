/*
 * HTML5 Forms Fallback for older and unsupporting browsers
 * Using jQuery, jQuery UI, Modernizr, Webforms2, and other jQuery Plugins
 * 
 * 2010 Cristian I. Colceriu
 * http://net.tutsplus.com/tutorials/html-css-techniques/how-to-build-cross-browser-html5-forms/
 * www.ghinda.net
 * contact@ghinda.net
 *
 */
 
/* Slide
 * input[type=range] fallback
 *
 * using jQuery UI Slider
 */
var initSlider = function() {			
	$('input[type=range]').each(function() {
		var $input = $(this);
		var $slider = $('<div id="' + $input.attr('id') + '" class="' + $input.attr('class') + '"></div>');
		var step = $input.attr('step');
		
		$input.after($slider).hide();
						
		$slider.slider({
			min: $input.attr('min'),
			max: $input.attr('max'),
			step: $input.attr('step'),
			change: function(e, ui) {
				$(this).val(ui.value);
			}
		});
	});
};

if(!Modernizr.inputtypes.range){
	$(document).ready(initSlider);
};

/* Numeric Spinner
 * input[type=number] fallback
 * 
 * using jQuery Spinner plugin by Brant Burnett(http://btburnett.com/)
 */
var initSpinner = function() {			
	$('input[type=number]').each(function() {
		var $input = $(this);
		$input.spinner({
			min: $input.attr('min'),
			max: $input.attr('max'),
			step: $input.attr('step')
		});
	});
};
if(!Modernizr.inputtypes.number){		
	$(document).ready(initSpinner);
};

/* Datepicker
 * input[type=date] fallback
 *
 * using jQuery UI Datepicker
 */
var initDatepicker = function() {
	$('input[type=date]').each(function() {
		var $input = $(this);
		$input.datepicker({
			minDate: $input.attr('min'),
			maxDate: $input.attr('max'),
			dateFormat: 'yy-mm-dd'
		});
	});
};

if(!Modernizr.inputtypes.date){
	$(document).ready(initDatepicker);
};

/* ColorPicker
 * input[type=color] fallback
 *
 * using jQuery ColorPicker plugin by Stefan Petre(http://www.eyecon.ro/)
 * http://www.eyecon.ro/colorpicker/
 */
var initColorpicker = function() {
	$('input[type=color]').each(function() {
		var $input = $(this);
		$input.ColorPicker({
			onSubmit: function(hsb, hex, rgb, el) {
				$(el).val(hex);
				$(el).ColorPickerHide();
			}
		});
	});			
};

if(!Modernizr.inputtypes.color){
	$(document).ready(initColorpicker);
};

/* Placeholder
 * placeholder attribute fallback
 *
 * using jQuery Placehold plugin by Viget Inspire(http://www.viget.com/inspire/)
 * http://www.viget.com/inspire/a-jquery-placeholder-enabling-plugin/
 */
var initPlaceholder = function() {
	$('input[placeholder]').placehold({
		placeholderClassName: 'placeholder'
	});
};

if(!Modernizr.input.placeholder){
	$(document).ready(initPlaceholder);
};