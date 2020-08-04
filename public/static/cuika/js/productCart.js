$(function() {
    
    // 加入购物袋
    $('.shop>.btn').click(function() {
      const _this = $(this);
      
      let goodsId = _this.attr("data-goodsid");
      
      productAjax(goodsId, _this);
      
    });
    

    function productAjax(goodsId, _this) {
      var options = {
        url: '/cart',
        type: 'POST',
        data: {
          goods_id: goodsId,
          stock: 1
        },
        dataType: 'JSON',
        getresult: function(data) {
          if(data.code == 0){
            _this.parent().parent().find('.success-msg').fadeIn();

            setTimeout(function() {
              _this.parent().parent().find('.success-msg').fadeOut(800);
            },1000);

          }else if(data.code == -400) {
            layer.msg(data.msg,{time: 1000,},function(){
              $('#loginAlert').fadeIn(200);
            });


          }else{
            console.log("加入购物车失败" + data.code);

          }
        }
      }
      sendRequest(options.url,options.type,options.data,options.getresult);
    }
    // 加入购物袋
})