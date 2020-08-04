<?php
/**
 * Created by yang
 * User: bonzaphp@gmail.com
 * Date: 2020-04-01
 * Time: 13:56
 */

use think\facade\Route;

Route::group('treca',static function(){
});
Route::get('product-cd-index','cuika/Product/mattress');//床垫
Route::get('product-cj-index','cuika/Product/bedstead');//床架
Route::get('product-pj-index','cuika/Product/accessories');//配件
Route::get('brandStory','cuika/Brand/index');//百年文化,百年崔卡
Route::get('jx','cuika/Originality/index');//匠心工艺

/** 床垫 */
Route::get('mattress','cuika/Mattress/Index');//床垫首页
Route::get('mattress/infinite','cuika/Mattress/Infinite');//无限系列
Route::get('mattress/planet-coordinates','cuika/Mattress/PlanetCoordinates');//星球坐标系列
Route::get('mattress/dreamland','cuika/Mattress/Dreamland');//梦境系列
Route::get('mattress/mel','cuika/Mattress/Mel');//梅尔系列
Route::get('mattress/leixoes-s-c','cuika/Mattress/LeixoesSC');//雷克索方系列

/** 床架 */
Route::get('bedstead','cuika/Bedstead/Index');//床架首页
Route::get('bedstead/infinite','cuika/Bedstead/Infinite');//无限系列
Route::get('bedstead/planet-coordinates','cuika/Bedstead/PlanetCoordinates');//星球坐标系列
Route::get('bedstead/dreamland','cuika/Bedstead/Dreamland');//梦境系列
Route::get('bedstead/mel','cuika/Bedstead/Mel');//梅尔系列
Route::get('bedstead/leixoes-s-c','cuika/Bedstead/LeixoesSC');//雷克索方系列

/** 配件 */
Route::get('accessory','cuika/Accessory/Index');//配件首页
Route::get('accessory/pillow','cuika/Accessory/Pillow');//枕头系列
Route::get('accessory/bedside','cuika/Accessory/Bedside');//床头柜系列
Route::get('accessory/bed-chair','cuika/Accessory/BedChair');//床尾凳系列

/** 分类 */
Route::get('category/:id','cuika/Category/Index');//分类


/** 商品详情 */
Route::get('product/detail/:goods_id','cuika/Product/Detail');//商品详情
Route::get('product/option/:goods_id','cuika/Product/Option');//规格
Route::get('product/search','cuika/Product/Search');//商品搜索
Route::post('product/spec-detail','cuika/Product/SpecDetail');//商品规格信息
Route::post('product/spec-type','cuika/Product/SpecType');//商品规格信息


/* @author chen */
// 匠心工艺
Route::get('jx-detail','cuika/Originality/jx');// 匠心
Route::get('jr-detail','cuika/Originality/jr');// 匠人
Route::get('zc-detail','cuika/Originality/zc');// 珍材
// 联系我们
Route::get('address','cuika/About/address');// 公司地址
Route::get('zsjm','cuika/About/zsjm');// 招商加盟
Route::get('after-sale','cuika/About/afterSale');// 售后质保
Route::get('alteration','cuika/About/alteration');// 退换货服务
Route::get('pr','cuika/About/pr');// 隐私政策
Route::get('redemption','cuika/About/redemption');// 换购计划
// 招聘信息
Route::get('recruit','cuika/Recruit/index');
// 门店信息
Route::get('treca-shop','cuika/About/shop');
Route::get('shop-region', 'cuika/About/region');
// 常见问题
Route::get('choose','cuika/Problem/choose');// 如何挑选
// 崔佧资讯
Route::resource('news', 'cuika/News');
// 账户设置
Route::resource('treca-user', 'cuika/Trecauser');
// 注册/登录/找回密码
Route::get('regInfo', 'cuika/Login/regInfo');// 注册页面
Route::post('reg', 'cuika/Login/Reg');// 注册-添加数据
Route::post('reg-sms', 'cuika/Login/RegVerifySend');// 注册-短信验证码
Route::get('loginInfo', 'cuika/Login/loginInfo');// 登录页面
Route::post('login', 'cuika/Login/login');// 登录-执行操作
Route::get('logout', 'cuika/Login/Logout');// 退出登录
Route::get('forgetPwdInfo', 'cuika/Login/ForgetPwdInfo');// 找回密码-页面
Route::post('forgetPwdCode', 'cuika/Login/ForgetPwdVerifySend');// 找回密码-执行操作
Route::post('forgetPwd', 'cuika/Login/ForgetPwd');// 找回密码-执行操作
// 省市区
Route::get('region', 'cuika/Region/index');
/* @author chen end */


/* @auther yang */

/*购物车相关*/
Route::get('cart','cuika/Cart/index');//购物袋列表
Route::get('api/cart','api/Cart/index');//购物袋列表
Route::post('cart','cuika/Cart/save');//添加购物袋
Route::put('cart/stock','cuika/Cart/stock');//更新购物袋
Route::delete('cart','cuika/Cart/delete');//从购物车中删除



Route::post('good/favor','cuika/Goods/favor');//添加收藏
Route::get('good/favor','api/UserGoodsFavor/Index');//收藏列表
Route::get('favor','cuika/Goods/favorList');//收藏页面
Route::get('pay/step1','cuika/Pay/step1');//支付步骤一
Route::get('pay/step2','cuika/Pay/step2');//支付步骤二

/* 订单相关 */
Route::get('order/pay/index','cuika/Order/Index');//订单查看
Route::post('order/cancel','cuika/Order/cancel');//订单取消
Route::delete('order/del','cuika/Order/delete');//订单取消
Route::post('order/add','cuika/Buy/Add');//添加订单

/* 地址相关 */
Route::get('address/index','cuika/Address/index');//地址列表
//Route::get('address/add','cuika/Useraddress/SaveInfo');//地址编辑添加页面
Route::get('address/edit','cuika/Address/edit');//地址编辑添加页面
Route::post('address','cuika/Address/save');//添加地址
Route::delete('address','cuika/Address/delete');//删除地址
/*品牌画册*/
Route::get('books','cuika/Books/index');//品牌画册

/* 支付相关 */
//Route::get('order/pay','cuika/Order/pay');//地址编辑添加页面

#由于后台的原因，暂时无法开启Miss路由
//Route::miss('');
