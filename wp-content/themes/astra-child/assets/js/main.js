jQuery(document).ready(function ($) {
 
  //首頁 關於我們 圖片輪播
 $('.index-image-carousel .slider-for').slick({
    slidesToShow: 1,
    slidesToScroll: 1,
    arrows: false,
    asNavFor: '.index-image-carousel .slider-nav'
  });
  $('.index-image-carousel .slider-nav').slick({
    slidesToShow: 1,
    slidesToScroll: 1,
    asNavFor: '.index-image-carousel .slider-for',
    arrows: true,
    dots: false,
    centerMode: true,
    focusOnSelect: true,
    fade: true,
    prevArrow: `<button type="button" class="slick-prev"><svg width="7" height="12" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M-2.10598e-07 0.980724L1.04108 -1.10361e-07L6.71157 5.34495C6.80297 5.43059 6.87551 5.53244 6.92502 5.64462C6.97452 5.7568 7 5.87711 7 5.99861C7 6.12012 6.97452 6.24042 6.92502 6.3526C6.87551 6.46479 6.80297 6.56663 6.71157 6.65227L1.04108 12L0.000980942 11.0193L5.32314 6L-2.10598e-07 0.980724Z" fill="#4B4B4B"/></svg></button>`,
    nextArrow: `<button type="button" class="slick-next"><svg width="7" height="12" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M-2.10598e-07 0.980724L1.04108 -1.10361e-07L6.71157 5.34495C6.80297 5.43059 6.87551 5.53244 6.92502 5.64462C6.97452 5.7568 7 5.87711 7 5.99861C7 6.12012 6.97452 6.24042 6.92502 6.3526C6.87551 6.46479 6.80297 6.56663 6.71157 6.65227L1.04108 12L0.000980942 11.0193L5.32314 6L-2.10598e-07 0.980724Z" fill="#4B4B4B"/></svg></button>`,
  });

  //首頁 案例見證輪播
  $('.index-case-card > .slider').slick({
    slidesToShow: 3,
    slidesToScroll: 1,
    arrows: true,
    dots: false,
    centerMode: true,
    // centerPadding: '20%',
    // autoplay: true,
    // autoplaySpeed: 5000,
    speed: 1000, // 切換速度設定為 1000 毫秒（=1秒）
    focusOnSelect: false, //點輪播圖就可以切換
    prevArrow: `<button type="button" class="slick-prev"><svg width="7" height="12" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M-2.10598e-07 0.980724L1.04108 -1.10361e-07L6.71157 5.34495C6.80297 5.43059 6.87551 5.53244 6.92502 5.64462C6.97452 5.7568 7 5.87711 7 5.99861C7 6.12012 6.97452 6.24042 6.92502 6.3526C6.87551 6.46479 6.80297 6.56663 6.71157 6.65227L1.04108 12L0.000980942 11.0193L5.32314 6L-2.10598e-07 0.980724Z" fill="#4B4B4B"/></svg><span class="text">上個案例</span></button>`,
    nextArrow: `<button type="button" class="slick-next"><span class="text">下個案例</span><svg width="7" height="12" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M-2.10598e-07 0.980724L1.04108 -1.10361e-07L6.71157 5.34495C6.80297 5.43059 6.87551 5.53244 6.92502 5.64462C6.97452 5.7568 7 5.87711 7 5.99861C7 6.12012 6.97452 6.24042 6.92502 6.3526C6.87551 6.46479 6.80297 6.56663 6.71157 6.65227L1.04108 12L0.000980942 11.0193L5.32314 6L-2.10598e-07 0.980724Z" fill="#4B4B4B"/></svg></button>`,
    responsive: [
      {
        breakpoint: 575,
        settings: {
          arrows: false,
          dots: true,
          centerMode: false,
          slidesToShow: 1,
          slidesToScroll: 1,
        }
      },
    ]
  });



/**
 * 
 * 
 * 文章頁 搜尋下拉選單
 * 
 * **/
// 初始隱藏 .dropdown-list
$(".dropdown-list").hide();

// 點擊 .dropdown-title 時切換 .dropdown-list
$(".dropdown-title").click(function(e) {
    e.stopPropagation(); // 阻止點擊事件冒泡
    
    var $dropdown = $(this).next(".dropdown-list");

    if ($dropdown.is(":visible")) {
        // 隱藏
        $dropdown.fadeOut(200);
        $(this).removeClass("active");
    } else {
        // 隱藏所有其他 .dropdown-list，避免多個下拉框同時展開
        $(".dropdown-list").fadeOut(200);
        $(".dropdown-title").removeClass("active");

        // 顯示當前的 .dropdown-list
        $dropdown.fadeIn(200);
        $(this).addClass("active");
    }
});

// 點擊頁面其他地方時隱藏 .dropdown-list
$(document).click(function() {
    $(".dropdown-list").fadeOut(200);
    $(".dropdown-title").removeClass("active");
});

// 點擊 .dropdown-list 內部時，阻止事件冒泡，避免被 document click 影響
$(".dropdown-list").click(function(e) {
    e.stopPropagation();
});

//全站搜尋
$('.header-search-btn').on('click', function() {
  $('.search-entire-site').fadeIn(300);
});

$('.search-close-btn').on('click', function() {
  $('.search-entire-site').fadeOut(300);
});




// 首頁影片自動撥放
const $videos = $('.auto-play video');

function checkVisibilityAndToggleVideo() {
  $videos.each(function () {
    const rect = this.getBoundingClientRect();
    const inView = rect.top < window.innerHeight * 0.75 && rect.bottom > window.innerHeight * 0.25;
    if (inView) {
      this.play().catch(() => {});
    } else {
      this.pause();
    }
  });
}

// 初始判斷一次（防止首次不播放）
checkVisibilityAndToggleVideo();

// 綁定 scroll & resize
$(window).on('scroll resize', function () {
  checkVisibilityAndToggleVideo();
});


















});
