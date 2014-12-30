function initContentHeight(){
    var win = $(window);
    var header = $('.header');
    var contentframe = $('.content-center');
    var footer = $('.footer');
    var out_height = win.height() - header.outerHeight(true) - footer.outerHeight(true) - 130;
    contentframe.css('height', out_height);
}

function divresize(block, headerHeight, footerHeight) {
//    var windowHeight = $(".wrapper").height(); //определяем высоту окна браузера
    windowHeight = $(window).height();
    $(block).css('height', windowHeight - headerHeight - footerHeight); //устанавливаем высоту блока(равно высоте окна за вычетом шапки и подвала)
}

function setTopPadding(){
    var padding_main = $(".header").height() + 45;
    $(".main").css('padding-top', padding_main);
}

function initHeights(){
    setTopPadding();
    initContentHeight();
    divresize('.goal', 100, 118);
}

setInterval(initHeights, 300);

$(function(){
    window.onresize = function(){
        initContentHeight();
        divresize('.goal', 125, 118);
    };

    $('body').on('click', '.btn-chat', function(){

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
            $(this).parent().animate({marginRight: "166"}, { duration: 200, queue: false });
            $('#pop-up-chat').animate({marginRight: "166"}, { duration: 200, queue: false });

            $('.list-friends').css('height', $(window).height() - 282);
            $(window).resize(function(){
                $('.list-friends').css('height', $(window).height() - 282);
            });

        }
        return false;

    });
});