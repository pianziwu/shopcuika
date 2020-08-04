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
use app\service\PaymentService;
use app\service\SeoService;
use app\service\UserService;
use think\response\View;

/**
 * 支付
 * @author   Devil
 * @blog     http://1000-x.cn/
 * @version  0.0.1
 * @datetime 2016-12-01T21:51:08+0800
 */
class Pay extends Common
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
    }

    /**
     * 支付流程第一步
     * @return View
     * @author bonzaphp@gmail.com
     */
    public function step1()
    {
        try {
            // 用户地址列表
            $data = UserService::UserAddressList(['user' => $this->user]);
            $this->assign('user_address_list', $data['data']);
            //购物车商品列表
            $base = $this->getCartInfo();
            $this->assign('cart_list', $base['cart_list']);
            $this->assign('base', $base);
            // 浏览器名称
            $this->assign('home_seo_site_title', SeoService::BrowserSeoTitle('购物车', 1));
            return $this->fetch('pay/zf1');
        } catch (\Exception $e) {
            return $this->fetch('public/tips_error');
        }

    }

    /**
     * 支付流程第二步
     * @return View
     * @author bonzaphp@gmail.com
     */
    public function step2()
    {
        try {
            // 用户地址列表
            $data = UserService::UserAddressList(['user' => $this->user]);
            $this->assign('user_address_list', $data['data']);//获取购物车商品列表
            $base = $this->getCartInfo();
            $this->assign('base', $base);
            $this->assign('cart_list', $base['cart_list']);//获取购物支付方式列表
            $payList = PaymentService::BuyPaymentList([]);
            $this->assign('pay_list', $payList);// 浏览器名称
            $this->assign('home_seo_site_title', SeoService::BrowserSeoTitle('购物车', 1));
            return $this->fetch('pay/zf2');
        } catch (\Exception $e) {
            return $this->fetch('public/tips_error');
        }
    }

    /**
     * 二维码支付展示
     * @author   Devil
     * @blog    http://1000-x.cn/
     * @version 1.0.0
     * @date    2018-09-28
     * @desc    description
     */
    public function Qrcode()
    {
        $params = input();
        if (empty($params['url']) || empty($params['order_no']) || empty($params['name']) || empty($params['msg'])) {
            $this->assign('msg', '参数有误');
            return $this->fetch('public/tips_error');
        } else {
            $this->assign('params', $params);
            return $this->fetch();
        }
    }

    /**
     * 获取购物车信息
     * @return array
     * @author bonzaphp@gmail.com
     */
    private function getCartInfo(): array
    {
        try {
            $cart_list = BuyService::CartList(['user' => $this->user]);
            $base = [
                'total_price' => empty($cart_list['data']) ? 0 : sprintf('%.2f',array_sum(array_column($cart_list['data'], 'total_price'))),
                'buy_count'   => empty($cart_list['data']) ? 0 : array_sum(array_column($cart_list['data'], 'stock')),
                'ids'         => empty($cart_list['data']) ? '' : implode(',', array_column($cart_list['data'], 'id')),
                'cart_list'   => $cart_list['data']
            ];
            return $base;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }
}