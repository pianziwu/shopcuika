<?php
/**
 *
 * Created by PhpStorm.
 * User: spirit_cjw@163.com
 * Date: 2020/4/10
 * Time: 16:39
 */

namespace app\cuika\controller;


use app\service\UserService;

class Trecauser extends Common
{

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
    }

    /**
     * 账户设置
     *
     * @method
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author: chen
     * @time: 2020/4/11 15:24
     */
    public function index()
    {
        $params = [
            'user'  => $this->user
        ];
        $address = UserService::UserDefaultAddress($params);
        $this->assign('address', $address['data']);

        $this->assign('home_seo_site_title', '账户设置');
        return $this->fetch('management');
    }

    /**
     * 地址修改
     *
     * @method
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * @author: chen
     * @time: 2020/4/11 16:55
     */
    public function save()
    {
        // 是否ajax请求
        if(!IS_AJAX)
        {
            $this->error('非法访问');
        }

        // 开始处理
        $params = input();
        $params['user'] = $this->user;

        return UserService::UserAddressSave($params);
    }

}