// page init
jQuery(function(){
	initOpenClose();
});

// open-close init
function initOpenClose() {




     jQuery('div.still-nav').OpenClose({
          activeClass:'open',
          opener:'span.btn-still',
          slider:'div.pop-up-slide',
          effect:'slide',
          animSpeed:100
     });
	
	jQuery('div.mes').OpenClose({
          activeClass:'open',
          opener:'span.btn-mes',
          slider:'div.pop-up-message-wrapper',
          effect:'slide',
          animSpeed:100
     });

	
	
     jQuery('div.adjournment-proceedings').OpenClose({
          activeClass:'open',
          opener:'a.open',
          slider:'div.adjournment-proceedings-content',
          effect:'slide',
          animSpeed:500
     });
	
	jQuery('li.user-cost').OpenClose({
          activeClass:'open',
          opener:'a.cost',
          slider:'div.pop-up-fill-holder',
          effect:'slide',
          animSpeed:50
     });
	
}

// open-close plugin
jQuery.fn.OpenClose = function(_options){
	// default options
	var _options = jQuery.extend({
		activeClass:'active',
		opener:'.opener',
		slider:'.slide',
		animSpeed: 400,
		animStart:false,
		animEnd:false,
		effect:'fade',
		event:'click'
	},_options);

	return this.each(function(){
		// options
		var _holder = jQuery(this);
		var _slideSpeed = _options.animSpeed;
		var _activeClass = _options.activeClass;
		var _opener = jQuery(_options.opener, _holder);
		var _slider = jQuery(_options.slider, _holder);
		var _animStart = _options.animStart;
		var _animEnd = _options.animEnd;
		var _effect = _options.effect;
		var _event = _options.event;
		if(_slider.length) {
			_opener.bind(_event,function(){
				if(!_slider.is(':animated')) {
					if(typeof _animStart === 'function') _animStart();
					if(_holder.hasClass(_activeClass)) {
						_slider[_effect=='fade' ? 'fadeOut' : 'slideUp'](_slideSpeed,function(){
							if(typeof _animEnd === 'function') _animEnd();
						});
						_holder.removeClass(_activeClass);
					} else {
						_holder.addClass(_activeClass);
						_slider[_effect=='fade' ? 'fadeIn' : 'slideDown'](_slideSpeed,function(){
							if(typeof _animEnd === 'function') _animEnd();
						});
					}
				}
				return false;
			});
			if(_holder.hasClass(_activeClass)) _slider.show();
			else _slider.hide();
		}
	});
}

jQuery(function(){
	initFixLayout();
});

function initFixLayout(){
	jQuery('div.box-discounts-entries div.box-row:nth-child(2n)').addClass('even');
	jQuery('ul.list li:nth-child(2n)').addClass('even');
};