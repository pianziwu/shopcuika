<?php
// +----------------------------------------------------------------------
// | ShopXO 国内领先企业级B2C免费开源电商系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011~2019 http://1000-x.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Devil
// +----------------------------------------------------------------------
namespace app\cuika\controller;

use app\service\BuyService;
use app\service\GoodsDetail;
use app\service\SeoService;
use think\facade\Request;

/**
 * 购物车
 * @author   Devil
 * @blog     http://1000-x.cn/
 * @version  0.0.1
 * @datetime 2016-12-01T21:51:08+0800
 */
class Cart extends Common
{
    /**
     * 构造方法
     * @author   Devil
     * @blog    http://1000-x.cn/
     * @version 1.0.0
     * @date    2018-11-30
     * @desc    description
     */
    public function __construct()
    {
        parent::__construct();
        // 是否登录,如果是首页，就跳过检查，然后由首页自己处理
        if (Request::action() !== 'index') {
            $this->IsLogin();
        }
    }

    /**
     * 首页
     * @author   Devil
     * @blog     http://1000-x.cn/
     * @version  0.0.1
     * @datetime 2017-02-22T16:50:32+0800
     */
    public function index()
    {
        $cart_list = BuyService::CartList(['user' => $this->user]);
        $this->assign('cart_list', $cart_list['data']);
        $base = [
            'total_price' => empty($cart_list['data']) ? 0 : sprintf('%.2f',array_sum(array_column($cart_list['data'], 'total_price'))),
            'buy_count'   => empty($cart_list['data']) ? 0 : array_sum(array_column($cart_list['data'], 'stock')),
            'ids'         => empty($cart_list['data']) ? '' : implode(',', array_column($cart_list['data'], 'id')),
        ];
        $this->assign('base', $base);
        // 浏览器名称
        $this->assign('home_seo_site_title', SeoService::BrowserSeoTitle('购物车', 1));
        $tpl = 'gwd';
        //检查是否有登录，根据登录状态渲染不同的页面
        if (empty($this->user)) {
            $tpl = 'gwd2';
        } else {
            if ($cart_list['code'] !== 0) {
                $tpl = 'gwd1';
            }
        }
        // 优选推荐
        $this->assign('recommend_list', GoodsDetail::recommend(4));
        return $this->fetch($tpl);
    }

    /**
     * 购物车保存
     * @param  Request  $request
     * @return array|bool|mixed|string
     * @author   Devil
     * @blog    http://1000-x.cn/
     * @version 1.0.0
     * @date    2018-09-13
     * @desc    description
     */
    public function save(Request $request)
    {
        // 是否ajax请求
        if (!IS_AJAX) {
            $this->error('非法访问');
        }
        $params = $request::post();
        $validate = new \app\cuika\validate\Cart();
        if (!$validate->scene('save')->check($params)) {
            return json(['code' => 400, 'msg' => $validate->getError()]);
        }
        $params['user'] = $this->user;
        return BuyService::CartAdd($params);
    }

    /**
     * 购物车删除
     * @author   Devil
     * @blog    http://1000-x.cn/
     * @version 1.0.0
     * @date    2018-09-14
     * @desc    description
     */
    public function delete()
    {
        // 是否ajax请求
        if (!IS_AJAX) {
            return $this->error('非法访问');
        }
        $params = Request::param();
        $params['user'] = $this->user;
        return BuyService::CartDelete($params);
    }

    /**
     * 数量保存
     * @author   Devil
     * @blog    http://1000-x.cn/
     * @version 1.0.0
     * @date    2018-09-14
     * @desc    description
     */
    public function stock(Request $request)
    {
        // 是否ajax请求
        if (!IS_AJAX) {
            $this->error('非法访问');
        }
        $params = $request::put();
        $params['user'] = $this->user;
        return BuyService::CartStock($params);
    }
}
