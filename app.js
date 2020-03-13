// console.log(document.cookie);
    
var playCount = getCookie('playCount');
var clearCount = getCookie('clearCount');
var gameoverCount = getCookie('gameoverCount');

if(!playCount){
    document.cookie = 'playCount=0';
}
if(!clearCount){
    document.cookie = 'clearCount=0';
}
if(!gameoverCount){
    document.cookie = 'gameoverCount=0';
}

function getCookie(key) {
    //cookieから値を取得する
    var cookieString = document.cookie;

    //要素ごとに ";" で区切られているので、 ";"　で切り出しを行う
    var cookieKeyArray = cookieString.split(";");

    //要素分ループを行う
    for (var i=0; i < cookieKeyArray.length; i++) {
        var targetCookie = cookieKeyArray[i];

        //前後のスペースをカットする
        targetCookie = targetCookie.replace(/^\s+|\s+$/g, "");

        var valueIndex = targetCookie.indexOf("=");
        if(targetCookie.substring(0, valueIndex) == key) {
            //キーが引数と一致した場合、値を返す
            // return decodeURI(targetCookie.slice(valueIndex + 1));
            return encodeURIComponent(targetCookie.slice(valueIndex + 1));
            //cookieに保存
            //document.cookie = 'name=' + encodeURIComponent('kazukichi');
        }
    }

    return "";
}

$('.js-set-playCount').on('click', function (){
    playCount++;
    document.cookie = 'playCount=' + playCount;
});

$('.js-get-playCount').html($.cookie('playCount'));

function load(){
    if(document.getElementById('gameclear') != null){
        console.log('road');
        clearCount++;
        document.cookie = 'clearCount=' + clearCount;
        document.getElementById('clear1').innerHTML = clearCount;
    }
    if(document.getElementById('gameover') != null){
        gameoverCount++;
        document.cookie = 'gameoverCount=' + gameoverCount;
        document.getElementById('gameover1').innerHTML = gameoverCount;
    }
}

$('.js-set-clearCount').on('load', function (){
    gameclearCount++;
    document.cookie = 'clearCount=' + clearCount;
});

$('.js-get-clearCount').html($.cookie('clearCount'));

$('.js-set-gameoverCount').on('load', function (){
    gameoverCount++;
    document.cookie = 'gameoverCount=' + gameoverCount;
});

$('.js-get-gameoverCount').html($.cookie('gameoverCount'));

$(function(){
    $('.js-show-modal').on('click', function () {
        // var modalWidth = $('.js-show-modal-target').width();
        // var windowWidth = $(window).width();
        // $('.js-show-modal-target').attr('style', 
        // 'margin-left: ' + (windowWidth/2 - modalWidth/2) + 'px')
        $('.js-show-modal-target').show();
        $('.js-show-modal-cover').show();
    });
});