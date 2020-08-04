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

use app\service\HomeService;
use app\service\StylistService;
use think\facade\Hook;

/**
 * 首页
 * @author   Devil
 * @blog     http://1000-x.cn/
 * @version  0.0.1
 * @datetime 2016-12-01T21:51:08+0800
 */
class Index extends Common
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
     * 首页
     *
     * @method
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author: chen
     * @time: 2020/4/14 13:57
     */
    public function Index()
    {
        // 条件
        $params = [
            'where' => [
                ['is_enable', 'eq', 1]
            ],
            'field' => ['id', 'chinese_name', 'synopsis', 'home_head_img'],
            'n' => 1000,
        ];

        // 大师列表
        $stylist = StylistService::StylistList($params);
        $this->assign('stylist', $stylist['data']);

        $data_list = HomeService::homeIndex();
        // 置顶视频
        $this->assign('top_info', $data_list['top_info']);
        // 商品列表
        $this->assign('goods_list', $data_list['goods_list']);
        // 联系方式
        $this->assign('contact', $data_list['contact']);

        return $this->fetch();
    }

    /**
     * 钩子处理
     * @author   Devil
     * @blog    http://1000-x.cn/
     * @version 1.0.0
     * @date    2019-04-22
     * @desc    description
     * @param   [array]           $params [输入参数]
     */
    private function PluginsHook($params = [])
    {
        // 楼层数据顶部钩子
        $hook_name = 'plugins_view_home_floor_top';
        $this->assign($hook_name.'_data', Hook::listen($hook_name,
            [
                'hook_name'     => $hook_name,
                'is_backend'    => false,
                'user'          => $this->user,
            ]));

        // 楼层数据底部钩子
        $hook_name = 'plugins_view_home_floor_bottom';
        $this->assign($hook_name.'_data', Hook::listen($hook_name,
            [
                'hook_name'     => $hook_name,
                'is_backend'    => false,
                'user'          => $this->user,
            ]));

        // 轮播混合数据底部钩子
        $hook_name = 'plugins_view_home_banner_mixed_bottom';
        $this->assign($hook_name.'_data', Hook::listen($hook_name,
            [
                'hook_name'     => $hook_name,
                'is_backend'    => false,
                'user'          => $this->user,
            ]));
    }
}
