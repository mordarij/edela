jQuery(function(){
    initContentHeight();
    window.onresize = function(event){
        initContentHeight();
    };
});

// content height init
function initContentHeight(){
    var win = $(window);
    var header = $('.header');
    var contentframe = $('.content-frame');
    var footer = $('.footer');
    var out_height = win.height() - header.outerHeight(true) - footer.outerHeight(true) - 106;
    contentframe.css('min-height', out_height);
}
initContentHeight();

// page init
jQuery(function(){
    initFixLayout();
});
function initFixLayout(){
    $('table tr:nth-child(2n)').addClass('even');
    $('table td:nth-child(1)').addClass('first-child');
    $('table td:nth-child(odd)').addClass('last-child');
    $('table th:nth-child(1)').addClass('first-child');
    $('div.levels div.tr:nth-child(2n)').addClass('even');
};

jQuery(function(){
    initContentHeight();
    window.onresize = function(event){
        initContentHeight();
    };
});

// content height init
function initContentHeight(){
    var win = $(window);
    var header = $('.header');
    var contentframe = $('.content-center');
    var footer = $('.footer');
    var out_height = win.height() - header.outerHeight(true) - footer.outerHeight(true) - 130;
    contentframe.css('height', out_height);
}
initContentHeight();

$(function () {
    function divresize(block, headerHeight, footerHeight) {
        var windowHeight = $(".wrapper").height(); //определяем высоту окна браузера
        $(block).css('height', windowHeight - headerHeight - footerHeight); //устанавливаем высоту блока(равно высоте окна за вычетом шапки и подвала)
    }
    divresize('.goal', 125, 118); //вызываем функцию изменения размера блока
    $(window).bind("resize", function(){ //при изменении размера окна вызываем функцию
        divresize('.goal', 125, 118);
    });
});

$(function () {
    function divresize(block, headerHeight, footerHeight) {
        var windowHeight = $("body").height(); //определяем высоту окна браузера
        $(block).css('height', windowHeight - headerHeight - footerHeight); //устанавливаем высоту блока(равно высоте окна за вычетом шапки и подвала)
    }
//    divresize('.landing-container', 50, 0); //вызываем функцию изменения размера блока
//    $(window).bind("resize", function(){ //при изменении размера окна вызываем функцию
//        divresize('.landing-container', 50, 0);
//    });
});


$(document).ready(function(){


	$('#p-code').change(function(){
		if ($('#promo').hasClass('hidden')) {
			$('#promo').removeClass('hidden');
		}
		else {
			$('#promo').addClass('hidden');
		}
	});


//	$(".fancybox").fancybox({
//		'padding': 0,
//		'autoScale': false,
//		'transitionIn': 'none',
//		'transitionOut': 'none',
//		'overlayColor': '#000',
//		'overlayOpacity': 0.6
//	});



    var anim_int = 500;

    $('.btn-close').click(function(){
        $(this).parent().parent().animate({
            top: "300px"
        }, anim_int);
        return false;
    });

    $('.see').click(function(){
        if ($(this).hasClass('icon-nosee')) {
            $(this).removeClass('icon-nosee')
        }
        else {
            $(this).addClass('icon-nosee')
        }
        return false;
    });

    $('.open').click(function(){
        $(this).parent().parent().parent().parent().parent().parent().find('.job').animate({
            top: "0"
        }, anim_int);
        return false;
    });

    $('.progresbar').click(function(){
        $(this).parent().parent().parent().find('.statistics-runtime').css('top','-100').animate({
            top: "0"
        }, anim_int);
        return false;
    });

    $('.btn-close-statistics-runtime').click(function(){
        $(this).parent().parent().animate({
            top: "-300px"
        }, anim_int);
        return false;
    });

    $('.note').click(function(){
        $(this).parent().parent().parent().parent().parent().parent().find('.notes-case').animate({
            top: "0"
        }, anim_int);
        return false;
    });



    $('.btn-minus').click(function(){
        var obj = $(this).parent().find('input');
        var v = parseInt(obj.val());
        if (v > 1) {
            obj.val(v - 1)
        }
        return false;
    });

    $('.btn-plus').click(function(){
        var obj = $(this).parent().find('input');
        var v = parseInt(obj.val());
        obj.val(v + 1);
        return false;
    });
    
    
//    $('#nestable3').nestable();
//
//    $('.planned-business').shapeshift({
//    	handle: ".btn-move",
//    	gutterY: 45,
//    	gutterX: 16,
//    	paddingX: 0,
//    	paddingY: 0,
//    	animationSpeed: 600,
//    	align: "left"
//    });
//
//
//    $('.lk-holder').shapeshift({
//    	handle: ".btn-move",
//    	selector: "*",
//    	enableDrag: true,
//    	enableCrossDrop: true,
//    	enableResize: true,
//    	enableTrash: false,
//    	colWidth: null,
//    	columns: null,
//    	minColumns: 3,
//    	autoHeight: true,
//    	maxHeight: null,
//    	minHeight: 100,
//    	gutterX: 28,
//    	gutterY: 35,
//    	paddingX: 10,
//    	paddingY: 10,
//    	animated: true,
//    	animateOnInit: false,
//    	animationSpeed: 225,
//    	animationThreshold: 100,
//    	dragClone: false,
//    	deleteClone: true,
//    });
//
//    $('.payment-history-holder').shapeshift({
//    	selector: "*",
//    	enableDrag: false,
//    	minColumns: 3,
//    	autoHeight: true,
//    	maxHeight: null,
//    	minHeight: 100,
//    	gutterX: 28,
//    	gutterY: 35,
//    	paddingX: 10,
//    	paddingY: 10
//    });





    $(document).mouseup(function(e){
    
    	if ($(e.target).attr('type') != 'search' && $(e.target).attr('type') != 'submit') {
    		$("input[type=search]").fadeOut(100);
    	}
    	e.preventDefault();
    });
    $('.form-search input[type=search]').hide();
    $('.form-search button[type=submit]').mouseover(function(){
    	$('.form-search input[type=search]').fadeIn(600).focus();
    });

    $('.add-case').click(function(){
    	var box_obj = $(this).parent().parent().find('.box-new-daily-deal');
    	box_obj.animate({
    		top: "0"
    	}, 500);
    	box_obj.find('.row button[type=reset]').click(function(){
    		box_obj.animate({
    			top: "-234"
    		}, 500);
    		return false;
    	});
    	return false;
    });

    $('.icon-play').each(function(){

       var parent_holder = $(this).parent().parent();
       var parent_sp = $(this).parent();
       var stop_btn = parent_holder.find('.icon-stop');
       var pause_btn = parent_sp.find('.icon-pause');
       var play_btn = $(this);

	  play_btn.click(function(){
	  	play_btn.css('z-index', '0');
	  	pause_btn.css('z-index', '10');
	  	play_btn.animate({
	  		opacity: 0
	  	}, {
	  		duration: 200,
	  		step: function(now, fx){
	  			pause_btn.css("opacity", 100 - now).css('z-index', '10');
	  		}
	  	}).css('z-index', '0');
	  	
	  	stop_btn.show(30);
	  	return false;
	  });
	  
	  pause_btn.click(function(){
	  	pause_btn.animate({
	  		opacity: 0
	  	}, {
	  		duration: 30,
	  		step: function(now, fx){
	  			play_btn.css("opacity", 100 - now).css('z-index', '10');
	  		}
	  	}).css('z-index', '0');
	  	return false;
	  });

       stop_btn.click(function(){ pause_btn.click(); stop_btn.hide(100); return false;});

    });


	$('.btn-chat').click(function(){

    	if($(this).hasClass('open'))
    	{
    		$(this).removeClass('open');
    		$(this).parent().animate({marginRight: "0"}, { duration: 200, queue: false });
    		$('#pop-up-chat').animate({marginRight: "0"}, { duration: 200, queue: false });
    		$(this).parent().find('#pop-up-chat').hide(50);

    	}
    	else
    	{
    		$(this).addClass('open');
    		$('#pop-up-chat').show();
    		$(this).parent().animate({marginRight: "176"}, { duration: 200, queue: false });
    		$('#pop-up-chat').animate({marginRight: "176"}, { duration: 200, queue: false });

    		$('.list-friends').css('height', $(window).height() - 282);
    		$(window).resize(function(){
        		$('.list-friends').css('height', $(window).height() - 282);
    		});

    	}
    	return false;

    });

    $('.logo a').hover(function(){
    	$(this).find("span").animate({
    		top: "0"
    	}, 500);
    }, function(){
    	$(this).find("span").animate({
    		top: "-31"
    	}, 500);
    });

    $('.ava').hover(function(){
    	$(this).parent().addClass('hover');
    }, function(){
    	$(this).parent().removeClass('hover');
    });



	var padding_main = $(".header").height() + 45;
	$(".main").css('padding-top', padding_main);


	function scrollToId(id){
		$('html, body').animate({
			scrollTop: $(id).offset().top
		}, 1000);
	}
	
	$('#landing-menu li a').click(function(){
		$('#landing-menu li').removeClass('active');
		$(this).parent().addClass('active');
		scrollToId($(this).attr('href'))
		return false;
	});

	// Cache selectors
	var topMenu = $("#landing-menu"), topMenuHeight = topMenu.outerHeight() + 15, menuItems = topMenu.find("a"), scrollItems = menuItems.map(function(){
		var item = $($(this).attr("href"));
		if (item.length) {
			return item;
		}
	});

    // Bind to scroll
    $(window).scroll(function(){
    	// Get container scroll position
    	var fromTop = $(this).scrollTop() + topMenuHeight;
    	
    	// Get id of current scroll item
    	var cur = scrollItems.map(function(){
    		if ($(this).offset().top < fromTop) 
    			return this;
    	});
    	// Get the id of the current element
    	cur = cur[cur.length - 1];
    	var id = cur && cur.length ? cur[0].id : "";
    	// Set/remove active class
    	menuItems.parent().removeClass("active").end().filter("[href=#" + id + "]").parent().addClass("active");
    });
});






$(document).ready(function(){
//	jQuery('.scroll-pane').jScrollPane({
//		autoReinitialise: true,
//		autoReinitialiseDelay: 100
//	});
//
//	jQuery('.goal-text-scroll').jScrollPane({
//		autoReinitialise: true,
//		autoReinitialiseDelay: 100
//	});
	
	$('.btn-setting-goals li:first').click(function(){
		$('.btn-setting-goals li').removeClass('active');
		$(this).addClass('active');
		$('#pop-up-setting-goals').addClass('briefly');
		return false;
	});
	
	$('.btn-setting-goals li:last').click(function(){
		$('.btn-setting-goals li').removeClass('active');
		$(this).addClass('active');
		$('#pop-up-setting-goals').removeClass('briefly');
		return false;
	});
	
	$('.count-number-of-executions li span').click(function(){
		$('.count-number-of-executions li').removeClass('active');
		$('#count-number-input').val($(this).html());
		$(this).parent().addClass('active');
	});
	if ($('.selectpicker').length) {
		$('.selectpicker').selectpicker('');
	}
	
});










