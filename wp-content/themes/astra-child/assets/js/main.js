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

//中心介紹 > 中心環境 輪播
 $('.center-carousel .slider-for').slick({
  slidesToShow: 1,
  slidesToScroll: 1,
  arrows: false,
  dots: false,
  asNavFor: '.center-carousel .slider-nav'
});
$('.center-carousel .slider-nav').slick({
  slidesToShow: 1,
  slidesToScroll: 1,
  asNavFor: '.center-carousel .slider-for',
  arrows: false,
  centerMode: true,
  focusOnSelect: true,
  fade: true,
  dots: true,
  // customPaging: function(slider, i) {
  //   return '<button type="button">' + (i + 1) + '</button>';
  // },
  prevArrow: `<button type="button" class="slick-prev"><svg width="7" height="12" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M-2.10598e-07 0.980724L1.04108 -1.10361e-07L6.71157 5.34495C6.80297 5.43059 6.87551 5.53244 6.92502 5.64462C6.97452 5.7568 7 5.87711 7 5.99861C7 6.12012 6.97452 6.24042 6.92502 6.3526C6.87551 6.46479 6.80297 6.56663 6.71157 6.65227L1.04108 12L0.000980942 11.0193L5.32314 6L-2.10598e-07 0.980724Z" fill="#4B4B4B"/></svg></button>`,
  nextArrow: `<button type="button" class="slick-next"><svg width="7" height="12" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M-2.10598e-07 0.980724L1.04108 -1.10361e-07L6.71157 5.34495C6.80297 5.43059 6.87551 5.53244 6.92502 5.64462C6.97452 5.7568 7 5.87711 7 5.99861C7 6.12012 6.97452 6.24042 6.92502 6.3526C6.87551 6.46479 6.80297 6.56663 6.71157 6.65227L1.04108 12L0.000980942 11.0193L5.32314 6L-2.10598e-07 0.980724Z" fill="#4B4B4B"/></svg></button>`,
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


/**
 * 
 * 
 * 從健檢預約(/health-check-up-appointment/)
 * 點擊對應按鈕到諮詢預約(/consultation-appointment/#tab-2)切換對應tab表單
 * 
 * **/

const hash = window.location.hash;

  if (hash && hash.startsWith('#tab-')) {
    const observer = new MutationObserver(() => {
      const el = document.querySelector(hash);
      if (el && el.offsetParent !== null) { // 確保元素可見
        el.dispatchEvent(new MouseEvent('click', {
          view: window,
          bubbles: true,
          cancelable: true
        }));
        observer.disconnect(); // 點擊後移除 observer
      }
    });

    // 監聽整個 tabs 區塊變動（初始化會產生變化）
    const target = document.querySelector('.tabs-forms');
    if (target) {
      observer.observe(target, {
        childList: true,
        subtree: true,
        attributes: true
      });
    }
  }

// 個人預約諮詢 - 是否做過全身健檢 欄位
  const $radios = $('input[name="fullcheck"]');
  const $input = $('#last-check-time input');

  $radios.on('change', function() {
    if ($(this).val() === '是') {
      $input.prop('disabled', false);
    } else {
      $input.prop('disabled', true).val('');
    }
  });



//搬動 reCAPTCHA v3到表單下方
var pagesNeedBadgeMove = [
  'consultation-appointment'
];

var currentSlug = window.location.pathname.replace(/^\/|\/$/g, '');
var isMovePage = pagesNeedBadgeMove.includes(currentSlug);

function moveBadge() {
  var $badge = $('.grecaptcha-badge');
  var $placeholder = $('.grecaptcha-placeholder');

  if ($badge.length && $placeholder.length) {
    $badge.appendTo($placeholder);
    $badge.removeClass('badge-hidden');
    return true;
  }
  return false;
}

function hideBadge() {
  var $badge = $('.grecaptcha-badge');
  if ($badge.length) {
    $badge.addClass('badge-hidden');
  }
}

function waitForBadge() {
  var checkInterval = setInterval(function() {
    if (isMovePage) {
      if (moveBadge()) {
        clearInterval(checkInterval);
      }
    } else {
      hideBadge();
      clearInterval(checkInterval);
    }
  }, 300);
}

if (typeof grecaptcha === 'undefined') {
  var waitForRecaptcha = setInterval(function() {
    if (typeof grecaptcha !== 'undefined' && grecaptcha.render) {
      clearInterval(waitForRecaptcha);
      waitForBadge();
    }
  }, 300);
} else {
  waitForBadge();
}




//團體預約諮詢 - 最低跟最高預算判斷
function validateMinBudget() {
  var $minBud = $('#min-bud');
  var minBudget = parseInt($minBud.val(), 10);
  var maxBudget = parseInt($('#max-bud').val(), 10);
  var isValid = true;

  $('#error-min-bud').text(''); // ⭐保留你的個別錯誤提示

  if (!isNaN(minBudget) && !isNaN(maxBudget)) {
    if (minBudget > maxBudget) {
      $('#error-min-bud').text('最低預算不能高於最高預算');
      isValid = false;
    }
  }
  return isValid;
}

function validateMaxBudget() {
  var $maxBud = $('#max-bud');
  var minBudget = parseInt($('#min-bud').val(), 10);
  var maxBudget = parseInt($maxBud.val(), 10);
  var isValid = true;

  $('#error-max-bud').text(''); // ⭐保留你的個別錯誤提示

  if (!isNaN(minBudget) && !isNaN(maxBudget)) {
    if (minBudget > maxBudget) {
      $('#error-max-bud').text('最高預算不能低於最低預算');
      isValid = false;
    }
  }
  return isValid;
}

// 👉 只負責控制送出按鈕能不能點，不處理其他 UI
function updateSubmitButtonStatus() {
  var isMinValid = validateMinBudget();
  var isMaxValid = validateMaxBudget();
  var $submitButton = $('.group.appointment-form-warp .wpcf7-submit');

  if (!isMinValid || !isMaxValid) {
    $submitButton.prop('disabled', true); // 阻止點擊
  } else {
    $submitButton.prop('disabled', false); // 可以點
  }
}

// blur 的時候檢查
$('#min-bud').on('blur', updateSubmitButtonStatus);
$('#max-bud').on('blur', updateSubmitButtonStatus);

updateSubmitButtonStatus();


// function validateMinBudget() {
//   var $minBud = $('#min-bud');
//   var minBudget = parseInt($minBud.val(), 10);
//   var maxBudget = parseInt($('#max-bud').val(), 10);
//   var isValid = true;
//   var $submitButton = $('.group.appointment-form-warp .wpcf7-submit');

//   $('#error-min-bud').text('');
//   $minBud.removeClass('wpcf7-not-valid').attr('aria-invalid', 'false');

//   if (!isNaN(minBudget) && !isNaN(maxBudget)) {
//     if (minBudget > maxBudget) {
//       $('#error-min-bud').text('最低預算不能高於最高預算');
//       $minBud.addClass('wpcf7-not-valid').attr('aria-invalid', 'true');
//       isValid = false;
//     }
//   }
//   return isValid;
// }

// function validateMaxBudget() {
//   var $maxBud = $('#max-bud');
//   var minBudget = parseInt($('#min-bud').val(), 10);
//   var maxBudget = parseInt($maxBud.val(), 10);
//   var isValid = true;

//   $('#error-max-bud').text('');
//   $maxBud.removeClass('wpcf7-not-valid').attr('aria-invalid', 'false');

//   if (!isNaN(minBudget) && !isNaN(maxBudget)) {
//     if (minBudget > maxBudget) {
//       $('#error-max-bud').text('最高預算不能低於最低預算');
//       $maxBud.addClass('wpcf7-not-valid').attr('aria-invalid', 'true');
//       isValid = false;
//     }
//   }
//   return isValid;
// }

// // 即時 blur 檢查
// $('#min-bud').on('blur', validateMinBudget);
// $('#max-bud').on('blur', validateMaxBudget);

// $('.group.appointment-form-warp form.wpcf7-form').on('submit', function (e) {
//   var isMinValid = validateMinBudget();
//   var isMaxValid = validateMaxBudget();

//   console.log('submit trigger', isMinValid, isMaxValid);

//   if (!isMinValid || !isMaxValid) {
//     // 阻止表單送出
//     e.preventDefault();
//     e.stopImmediatePropagation();
//     return false;
//   }
// });


function goBack(url) {
  if (document.referrer && window.history.length > 1) {
      window.history.back();
  } else {
      window.location.href = url;
  }
}

$('.back-btn').on('click', function () {
  const fallbackUrl = $(this).data('fallback-url');
  goBack(fallbackUrl);
});


$('.is-loading-btn').on('click', function () {
  $('.is-loading').fadeIn();

  // 模擬任務完成後移除 loading（如 ajax 完成）
  setTimeout(function () {
    $('.is-loading').fadeOut();
  }, 3000);
});

});



