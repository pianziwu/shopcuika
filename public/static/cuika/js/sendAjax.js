// 公用ajax方法
function sendRequest(url,type,data,callback) {
  return $.ajax({
    url: url,
    type: type,
    dataType: "JSON",
    data: data,
    success: function(msg){
      callback(msg)
    },
    error: function(err){
      layer.msg('服务器繁忙，请稍后再试');
    }
  });
}