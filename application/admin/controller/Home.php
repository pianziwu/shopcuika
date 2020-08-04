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
namespace app\admin\controller;

use app\service\HomeService;

/**
 * 首页管理
 * @author   Devil
 * @blog     http://1000-x.cn/
 * @version  0.0.1
 * @datetime 2016-12-01T21:51:08+0800
 */
class Home extends Common
{
    protected $service;
    /**
     * 构造方法
     * @author   Devil
     * @blog     http://1000-x.cn/
     * @version  0.0.1
     * @datetime 2016-12-03T12:39:08+0800
     */
    public function __construct()
    {
        // 调用父类前置方法
        parent::__construct();

        // 登录校验
        $this->IsLogin();

        // 权限校验
        $this->IsPower();

        $this->service = new HomeService();
    }

    /**
     * [Index 轮播图片列表]
     * @author   Devil
     * @blog     http://1000-x.cn/
     * @version  0.0.1
     * @datetime 2016-12-06T21:31:53+0800
     */
    public function Index()
    {
        // 参数
        $this->assign('params', input());
        // 数据
        $data = $this->service->getList();
        $this->assign('data_list', $data);
        return $this->fetch();
    }

    /**
     * [SaveInfo 添加/编辑页面]
     * @author   Devil
     * @blog     http://1000-x.cn/
     * @version  0.0.1
     * @datetime 2016-12-14T21:37:02+0800
     */
    public function SaveInfo()
    {
        // 参数
        $params = input();

        // 数据
        $data = $this->service->detail($params);
        $this->assign('data_list', $data);

        // 类型
        $this->assign('show_type', $this->service->showType());

        // 参数
        $this->assign('params', $params);

        return $this->fetch();
    }

    /**
     * [Save 轮播图片添加/编辑]
     * @author   Devil
     * @blog     http://1000-x.cn/
     * @version  0.0.1
     * @datetime 2016-12-14T21:37:02+0800
     */
    public function Save()
    {
        // 是否ajax请求
        if(!IS_AJAX)
        {
            return $this->error('非法访问');
        }

        // 开始处理
        $params = input();
        return HomeService::Save($params);
    }

    /**
     * [Delete 轮播图片删除]
     * @author   Devil
     * @blog     http://1000-x.cn/
     * @version  0.0.1
     * @datetime 2016-12-15T11:03:30+0800
     */
    public function Delete()
    {
        // 是否ajax请求
        if(!IS_AJAX)
        {
            return $this->error('非法访问');
        }

        // 开始处理
        $params = input();
        return $this->service->Delete($params);
    }

    /**
     * [StatusUpdate 状态更新]
     * @author   Devil
     * @blog     http://1000-x.cn/
     * @version  0.0.1
     * @datetime 2017-01-12T22:23:06+0800
     */
    public function StatusUpdate()
    {
        // 是否ajax请求
        if(!IS_AJAX)
        {
            return $this->error('非法访问');
        }

        // 开始处理
        $params = input();
        return $this->service->StatusUpdate($params);
    }
}
