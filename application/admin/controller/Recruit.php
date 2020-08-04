<?php
/**
 * 招聘信息
 * Created by PhpStorm.
 * User: spirit_cjw@163.com
 * Date: 2020/4/9
 * Time: 9:28
 */

namespace app\admin\controller;


use app\service\RecruitService;
use base\Page;

class Recruit extends Common
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

        // 权限校验
        $this->IsPower();
    }

    /**
     * 招聘列表
     *
     * @method
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author: chen
     * @time: 2020/4/9 11:05
     */
    public function Index()
    {
        // 参数
        $params = input();

        // 分页
        $number = MyC('admin_page_number', 10, true);

        // 条件
        $where = RecruitService::RecruitListWhere($params);

        // 获取总数
        $total = RecruitService::RecruitTotal($where);

        // 分页
        $page_params = array(
            'number'    =>  $number,
            'total'     =>  $total,
            'where'     =>  $params,
            'page'      =>  isset($params['page']) ? intval($params['page']) : 1,
            'url'       =>  MyUrl('admin/recruit/index'),
        );
        $page = new Page($page_params);
        $this->assign('page_html', $page->GetPageHtml());

        // 获取列表
        $data_params = array(
            'm'     => $page->GetPageStarNumber(),
            'n'     => $number,
            'where' => $where,
            'field' => '*',
        );
        $data = RecruitService::RecruitList($data_params);
        $this->assign('data_list', $data['data']);

        // 是否启用
        $this->assign('common_is_enable_list', lang('common_is_enable_list'));

        // 参数
        $this->assign('params', $params);
        return $this->fetch();
    }

    /**
     * 招聘添加/编辑页面
     *
     * @method
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author: chen
     * @time: 2020/4/9 11:14
     */
    public function SaveInfo()
    {
        // 参数
        $params = input();

        // 数据
        $data = [];
        if(!empty($params['id']))
        {
            // 获取列表
            $data_params = array(
                'm'     => 0,
                'n'     => 1,
                'where' => ['id'=>intval($params['id'])],
                'field' => '*',
            );
            $ret = RecruitService::RecruitList($data_params);
            $data = empty($ret['data'][0]) ? [] : $ret['data'][0];
        }

        // 是否启用
        $this->assign('common_is_enable_list', lang('common_is_enable_list'));

        // 招聘编辑页面钩子
//        $hook_name = 'plugins_view_admin_Recruit_save';
//        $this->assign($hook_name.'_data', Hook::listen($hook_name,
//            [
//                'hook_name'     => $hook_name,
//                'is_backend'    => true,
//                'Recruit_id'    => $params['id'] ?? 0,
//                'data'          => &$data,
//                'params'        => &$params,
//            ]));

        // 编辑器文件存放地址
        $this->assign('editor_path_type', 'Recruit');

        // 数据
        $this->assign('data', $data);
        $this->assign('params', $params);
        return $this->fetch();
    }

    /**
     * 招聘添加/编辑
     *
     * @method
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @author: chen
     * @time: 2020/4/9 11:35
     */
    public function Save()
    {
        // 是否ajax请求
        if(!IS_AJAX)
        {
            $this->error('非法访问');
        }

        // 开始处理
        $params = input();
        return RecruitService::RecruitSave($params);
    }

    /**
     * [Delete 招聘删除]
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
            $this->error('非法访问');
        }

        // 开始处理
        $params = input();
        $params['admin'] = $this->admin;
        return RecruitService::RecruitDelete($params);
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
            $this->error('非法访问');
        }

        // 开始处理
        $params = input();
        $params['admin'] = $this->admin;
        $params['field'] = 'is_enable';
        return RecruitService::RecruitStatusUpdate($params);
    }
}