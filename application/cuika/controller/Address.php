<?php
/**
 * Created by yang
 * User: bonzaphp@gmail.com
 * Date: 2020-04-10
 * Time: 17:11
 */

namespace app\cuika\controller;

use app\service\SeoService;
use app\service\UserService;
use think\Request;

/**
 * 地址管理
 * Class Address
 * @author bonzaphp@gmail.com
 * @Date 2020-04-14 09:17
 * @package app\cuika\controller
 */
class Address extends Common
{
    public function __construct()
    {
        parent::__construct();

        // 是否登录
        $this->IsLogin();
    }

    /**
     * @return mixed
     * @author bonzaphp@gmail.com
     */
    public function index()
    {

        try {// 用户地址列表
            $data = UserService::UserAddressList(['user' => $this->user]);
            $this->assign('user_address_list', $data['data']);// 浏览器名称
            $this->assign('home_seo_site_title', SeoService::BrowserSeoTitle('我的地址', 1));
            return $this->fetch();
        } catch (\Exception $e) {
            $this->assign('msg',$e->getMessage());
            return $this->fetch('public/tips_error');
        }
    }

    /**
     * 地址添加修改页面
     * @return mixed
     * @author bonzaphp@gmail.com
     */
    public function edit()
    {
        try {
            $this->assign('is_header', 0);
            $this->assign('is_footer', 0);
            if (input()) {
                $params = input();
                $params['user'] = $this->user;
                $data = UserService::UserAddressRow($params);
                $this->assign('data', $data['data']);
            } else {
                $this->assign('data', []);
            }
            return $this->fetch('address/edit');
        } catch (\Exception $e) {
            $this->assign('msg',$e->getMessage());
            return $this->fetch('public/tips_error');
        }
    }

    /**
     * 新增地址
     * @return \think\response\Json
     * @author bonzaphp@gmail.com
     */
    public function save(): \think\response\Json
    {
        /**
         * 本接口为ajax调用
         */
        try {
            $params = input('post.');
            $params['user'] = $this->user;
            $validate = new \app\cuika\validate\Address();
            if (!$validate->scene('save')->check($params)) {
                return json(['code' => 400, 'msg' => $validate->getError()]);
            }
            $res = UserService::UserAddressSave($params);
            return json($res)->code(200);
        } catch (\Exception $e) {
            return json(['code'=> 400,'msg'=>$e->getMessage()]);
        }
    }

    /**
     * 删除地址
     * @method ajax
     * @param  Request  $request
     * @return \think\response\Json
     * @author bonzaphp@gmail.com
     */
    public function Delete(Request $request): \think\response\Json
    {
        $params = $request->param();
        $params['user'] = $this->user;
        return json(UserService::UserAddressDelete($params))->code(200);
    }
}