var cartbox = {
	totalNum:{
	 	totalBtn1 : 0,
		totalBtn2 : 0
	},	
	clickBtn:function (img,num) {
		let flyImg = img.clone().css({
			'opacity':'0.6'
		});
		$('body').append(flyImg);
		flyImg.css({
			'z-index':999,
			'border':'3px solid #fff',
			'position': 'absolute',
			'height' : img.height() + 'px',
			'width' : img.width() + 'px',
			'top' : img.offset().top +'px',
			'left' : img.offset().left + 'px'
		})
		flyImg.animate({
			'width' : 50 + 'px',
			'height' : 50 + 'px',
			'border-radius' : 100 + '%'
		},function(){
			let t;
			t = $('#btn1-add').offset().top;
			cartbox.totalNum.totalBtn1 = num;
			if(cartbox.totalNum.totalBtn1>99){
				cartbox.totalNum.totalBtn1 = 99
			}
			flyImg.animate({
				'top':t,
				'left':($(document).width()-$('.right-cartBox').width()) + 'px',
				'height':0 +'px',
				'width' :0+'px',
			},1500,function(){
				flyImg.remove();
				$('#btn2-add').html(cartbox.totalNum.totalBtn2);
				$('#btn1-add').html(cartbox.totalNum.totalBtn1);
			})
		});
	},
	mouseCover: function(){
		$('.right-cartBox-ul li').on('mouseenter',function(){
			$(this).find('.leftBox').animate({
				'left':-90 +'px',
			}).addClass('show');
			$(this).find('.fir-leftBox').addClass("show");
			$(this).find('.sev-leftBox').addClass("show");
		})
		$('.right-cartBox-ul li').on('mouseleave',function(){
			$(this).find('.leftBox').animate({
				'left':-121+'px',
			}).add('.fir-leftBox').removeClass('show');
			$(this).find('.fir-leftBox').removeClass('show');
			$(this).find('.sev-leftBox').removeClass('show');
		})
	},
	
	init: function (num) {
		cartbox.totalNum.totalBtn1= num? num : 0;
		jQuery(document).ready(function ($) {
			var rightCart='<ul class="right-cartBox-ul"><li class="sec"><a href="/cart"><i><svg t="1586415943681" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="5590" width="40" height="40"><path d="M200.64 184.736H128a24 24 0 0 1 0-48h92.416a24 24 0 0 1 23.552 19.424l19.712 101.696h607.264a56 56 0 0 1 53.92 71.168l-74.176 263.488a120 120 0 0 1-115.52 87.488H395.584a120 120 0 0 1-117.792-97.152L200.64 184.736zM272.96 305.856l51.936 267.84a72 72 0 0 0 70.656 58.304h339.584a72 72 0 0 0 69.312-52.48l74.176-263.52a8 8 0 0 0-7.68-10.144H272.96zM720 872a88 88 0 1 1 0-176 88 88 0 0 1 0 176z m0-48a40 40 0 1 0 0-80 40 40 0 0 0 0 80z m-320 48a88 88 0 1 1 0-176 88 88 0 0 1 0 176z m0-48a40 40 0 1 0 0-80 40 40 0 0 0 0 80z m-24-392a24 24 0 1 1 0-48 24 24 0 0 1 0 48z m38.944 96a24 24 0 1 1 0-48 24 24 0 0 1 0 48z m57.056-96a24 24 0 1 1 0-48 24 24 0 0 1 0 48z m43.488 96a24 24 0 1 1 0-48 24 24 0 0 1 0 48z m52.512-96a24 24 0 1 1 0-48 24 24 0 0 1 0 48z m48 96a24 24 0 1 1 0-48 24 24 0 0 1 0 48z m48-96a24 24 0 1 1 0-48 24 24 0 0 1 0 48z m48 96a24 24 0 1 1 0-48 24 24 0 0 1 0 48z m48-96a24 24 0 1 1 0-48 24 24 0 0 1 0 48z" fill="#99cc99" p-id="5591"></path></svg></i><span id="btn1-add">'+cartbox.totalNum.totalBtn1+'</span></a><div class="leftBox">我的购物车</div></li><li class="fif" style="display:none;"><a href="javascript:;"><i>收藏</i><span id="btn2-add">0</span></a><div class="leftBox">我的收藏</div></li></ul><style>.right-cartBox{position:fixed;top:40%;right:30px;width:42px;}.right-cartBox-ul{margin-top:0}.right-cartBox-ul li{position:relative;margin-bottom:20px}.right-cartBox-ul li a{display:block;width:40px;min-height:50px;text-decoration:none}.sec i,.fif i{display:block;height:40px;width:40px;}.sec .con{height:48px;width:16px;color:#fff;font-size:14px;margin:5px 0 0 12px}.leftBox{width:92px;height:38px;background:#8d6945;position:absolute;left:-121px;top:2px;display:none;color:#fff;line-height:38px;text-align:center;font-size:14px}.sec span,.fif span{position:absolute;top:-24px;right:-10px;height:21px;width:21px;border-radius:100%;background-color:#fcfbf5;color:#333;margin:15px 0 0 10px;text-align:center;line-height:21px}.fif i{border:1px solid #000}.fif span{background-color:#f40}.show{}</style>'
			$('<div class="right-cartBox">'+rightCart+"</div>").appendTo("body");
			cartbox.mouseCover();
		});
	}
};