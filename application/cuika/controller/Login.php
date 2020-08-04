<?php
/**
 *
 * Created by PhpStorm.
 * User: spirit_cjw@163.com
 * Date: 2020/4/6
 * Time: 10:43
 */

namespace app\cuika\controller;


use app\service\SeoService;
use app\service\UserService;

class Login extends Common
{

    /**
     * 注册页面
     *
     * @method
     * @return bool|mixed
     * @author: chen
     * @time: 2020/4/6 10:55
     */
    public function regInfo()
    {
        $reg_all = MyC('home_user_reg_state');
        if(!empty($reg_all))
        {
            if(empty($this->user))
            {
                return $this->fetch('createAccount');
            } else {
                $this->error('已经登录了，如要注册新账户，请先退出当前账户');
            }
        } else {
            $this->error('暂时关闭用户注册');
        }
        return false;
    }

    /**
     * 注册-发送验证码
     *
     * @method
     * @return array|bool|mixed|string
     * @author: chen
     * @time: 2020/4/8 13:59
     */
    public function RegVerifySend()
    {
        // 是否ajax请求
        if(!IS_AJAX)
        {
            $this->error('非法访问');
        }
        // 调用服务层
        return UserService::RegVerifySend(input('post.'));
    }

    /**
     * 注册-添加用户
     *
     * @method
     * @return array|bool|mixed|string
     * @author: chen
     * @time: 2020/4/6 11:01
     */
    public function Reg()
    {
        // 是否ajax请求
        if(!IS_AJAX)
        {
            $this->error('非法访问');
        }

        // 调用服务层
        return UserService::Reg(input('post.'));
    }

    /**
     * 登录页面
     *
     * @method
     * @return bool|mixed
     * @author: chen
     * @time: 2020/4/6 15:42
     */
    public function loginInfo()
    {
        if(MyC('home_user_login_state') == 1)
        {
            if(empty($this->user))
            {
                return $this->fetch('login');
            } else {
                $this->error('已经登录了，请勿重复登录');
            }
        } else {
            $this->error('暂时关闭用户登录');
        }
        return false;
    }

    /**
     * 登录-执行操作
     *
     * @method
     * @return array|mixed
     * @author: chen
     * @time: 2020/4/6 15:42
     */
    public function login()
    {
        // 是否ajax请求
        if(!IS_AJAX)
        {
            $this->error('非法访问');
        }
        // 调用服务层
        return UserService::Login(input('post.'));
    }

    /**
     * 找回密码-页面
     *
     * @method
     * @return mixed
     * @author: chen
     * @time: 2020/4/6 15:50
     */
    public function ForgetPwdInfo()
    {
        if(empty($this->user))
        {
            return $this->fetch('forgetPwd');
        } else {
            $this->error('已经登录了，如要重置密码，请先退出当前账户');
            return true;
        }
    }

    /**
     * 找回密码验证码获取
     *
     * @method
     * @return array
     * @author: chen
     * @time: 2020/4/9 17:28
     */
    public function ForgetPwdVerifySend()
    {
        // 是否ajax请求
        if(!IS_AJAX)
        {
            $this->error('非法访问');
        }

        // 调用服务层
        return UserService::ForgetPwdVerifySend(input('post.'));
    }

    /**
     * 找回密码-执行
     *
     * @method
     * @return array|bool|mixed|string
     * @author: chen
     * @time: 2020/4/6 15:45
     */
    public function ForgetPwd()
    {
        // 是否ajax请求
        if(!IS_AJAX)
        {
            $this->error('非法访问');
        }

        // 调用服务层
        return UserService::ForgetPwd(input('post.'));
    }

    /**
     * 退出登录
     *
     * @method
     * @return mixed
     * @author: chen
     * @time: 2020/4/8 15:19
     */
    public function Logout()
    {
        // 调用服务层
        UserService::Logout();

        $this->redirect('/');
        return true;
    }

}