$(function(){
  //移动端导航
  $('.menu-toggle-btn').click(function(){
    $(this).parent('.navigation-menu').toggleClass('active');
  })

  $('.navigation-menu .menu-item .toggle-btn').click(function(){
    $(this).toggleClass('open');
    $(this).parents('.menu-item').find('.drop-down').slideToggle(350);
  })

  $('.search-icon-box').click(function(){
    $('.search-form').addClass('open');
    if($('.search-ipt').val() == ''){
      return;
    }else{
      $('.search-form form').submit();
    }
  })
  $('body').click(function(e){
    if($(e.target).attr('class')=='search-icon' || $(e.target).attr('class')=='search-ipt' || $(e.target).attr('class')=='search-icon-box'){
      $('.search-btns').addClass('mopen');
      $('.search-form').addClass('open');
      $('.logo-chanel').addClass('opacity');
    }else{
      $('.search-btns').removeClass('mopen');
      $('.search-form').removeClass('open');
      $('.logo-chanel').removeClass('opacity');
    }
  });

  // 导航高亮
  var wLink = window.location.pathname;
  var navLink = $('.nav-list .menu-item a');
  var homeLink = '/';
  
  for(var i = 0; i < navLink.length; i++){
    var currtLink = $(navLink[i]).attr('href');
    if(currtLink.indexOf(wLink) != -1){
      var navCurrentIndex = $('.nav-list .menu-item a').eq(i).parents('.menu-item').index();
      $('.nav-list .menu-item').eq(navCurrentIndex).addClass('on').siblings().removeClass('on');
    }
  }

  if(wLink == homeLink){
    $('.nav-list .menu-item').removeClass('on');
  }

  // $(window).scroll(function(e){
  //   if($(window).width() > 992){
  //     if($(window).scrollTop() > 1){
  //       $('.logo-chanel').addClass('translatehide');
  //       $('.navbar').addClass('fixed');
  //       $('header').addClass('fixed');
  //     }else{
  //       $('.logo-chanel').removeClass('translatehide');
  //       $('.navbar').removeClass('fixed');
  //       $('header').removeClass('fixed');
  //     }
  //   }
  // })

  $(document).on("mousewheel DOMMouseScroll", function (e) {
    var delta = (e.originalEvent.wheelDelta && (e.originalEvent.wheelDelta > 0 ? 1 : -1)) || 
        (e.originalEvent.detail && (e.originalEvent.detail > 0 ? -1 : 1));
    if($(window).width() > 992){
      if (delta > 0) {
        $('.logo-chanel').removeClass('translatehide');
          $('.navbar').removeClass('fixed');
          $('header').removeClass('fixed');
          $('.slide-cart').removeClass('scrolling');
      } else if (delta < 0) {
        $('.logo-chanel').addClass('translatehide');
        $('.navbar').addClass('fixed');
        $('header').addClass('fixed');
        $('.slide-cart').addClass('scrolling');
      } else {

      }
    }
    
  });

  $('.slide-cart,.agreement,.responsibility').on("mousewheel DOMMouseScroll",function(e){
    e.stopPropagation()
  }) 

  // 切换个人中心
  $('header .personal').click(function(){
    $('.slide-cart').toggleClass('slideShow');
    if(/Android|webOS|iPhone|iPod|BlackBerry/i.test(navigator.userAgent)) {
      $('#index-video').hide();
    }
  })

  $('.slide-cart').click(function(e){
    var className =  $('.slide-cart').attr('class');
    if(($(e.target).attr('class') == className)){
      $('.slide-cart').removeClass('slideShow');
    }
  })

  $('.slide-cart-close').click(function(){
    $('#index-video').show();
    $('.slide-cart').removeClass('slideShow');
  })
  
   // 获取slide-item宽高
  var aIndex = 0;
  var sW = $('.index-page .s5 .content .slide-item img').eq(0).width();
  var sH = $('.index-page .s5 .content .slide-item img').eq(0).height();
  var forList = [2,1,0,1];
  var slideDoms = $('.index-page .s5 .content .slide-item');
  for(var i = 0,len = slideDoms.length; i < len; i ++){
    slideDoms.eq(i).attr('data-index',i);
    $('.index-page .s5 .content .wrapper').append(slideDoms.eq(i).clone());
  }

  $(window).scroll(function(){
    sW = $('.index-page .s5 .content .slide-item img').eq(0).width();
    sH = $('.index-page .s5 .content .slide-item img').eq(0).height();
    slideInit();
    cssDefault();
  })
  slideInit();
  function slideInit(){
    
    if($(window).width() > 1200){
      $('.index-page .s5').height(sH * 4);
    }else{
      $('.index-page .s5').css({
        height: 'auto'
      });
    }
    $('.index-page .s5 .swiper-box').css({
      height: sH * 4
    })
  
    $('.swiper-btns').css({
      width: sW,
      height: sH,
      left: sW,
      top: sH * 2
    })

    $('.swiper-show-container').css({
      right: sW * 2,
      top: sH,
      height: sH * 2
    })
  }

  cssDefault();
  function cssDefault(){
    slideDoms = $('.index-page .s5 .content .slide-item');
    for(var i = 0,len = slideDoms.length; i < len; i ++){
      var forIndex = i % 4;
      slideDoms.eq(i).css({
        left: sW * forList[forIndex],
        top: sH * ( i - 2)
      }) 
    }
    slideDoms.eq(forList.length).addClass('slide-item-active').siblings().removeClass('slide-item-active');
  }
  
  function nextPlay(aIndex){
    slideDoms = $('.index-page .s5 .content .slide-item');
    for(var i = 0,len = slideDoms.length; i < len; i ++){
      var forIndex = i % 4;
      slideDoms.eq(i).css({
        left: sW * forList[forIndex-aIndex],
        top: sH * (i - aIndex)
      })
    }
    $('.index-page .s5 .content .wrapper').append(slideDoms.eq(0))
    cssDefault();
    var activeDomIndex = $('.index-page .s5 .content .slide-item-active').attr('data-index');
    $('.swiper-show-container .show-box').eq(activeDomIndex).addClass('active').siblings().removeClass('active');
  }

  function prevPlay(aIndex){
    slideDoms = $('.index-page .s5 .content .slide-item');
    for(var i = 0,len = slideDoms.length; i < len; i ++){
      var forIndex = i % 4;
      slideDoms.eq(i).css({
        left: sW * forList[forIndex-aIndex],
        top: sH * (i - aIndex)
      })
    }
    $('.index-page .s5 .content .wrapper').prepend(slideDoms.eq(slideDoms.length - 1))
    cssDefault();
    var activeDomIndex = $('.index-page .s5 .content .slide-item-active').attr('data-index');
    $('.swiper-show-container .show-box').eq(activeDomIndex).addClass('active').siblings().removeClass('active');
  }

  $('.swiper-btns .swiper-btn-next').click(function(){
    aIndex++;
    nextPlay(aIndex);
  })
  $('.swiper-btns .swiper-btn-prev').click(function(){
    aIndex--;
    prevPlay(aIndex);
  })

  // var slideTimer = setInterval(function(){
  //   nextPlay(aIndex);
  // },2000)


  // $('.swiper-box').mouseover(function(){
  //   clearInterval(slideTimer);
  //   slideTimer = null;
  // })

  // $('.swiper-box').mouseleave(function(){
  //   slideTimer = setInterval(function(){
  //     nextPlay(aIndex);
  //   },2000)
  // })

  $('.index-page .s5 .content .slide-item').click(function(){
    var activeDomIndex = $(this).attr('data-index');
    $('.swiper-show-container .show-box').eq(activeDomIndex).addClass('active').siblings().removeClass('active');
  })
})
