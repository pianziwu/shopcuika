<?php
/**
 *
 * Created by PhpStorm.
 * User: spirit_cjw@163.com
 * Date: 2020/4/9
 * Time: 14:13
 */

namespace app\admin\controller;


use app\service\TrecaShopService;
use base\Page;

class Trecashop extends Common
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
        $where = TrecaShopService::TrecaShopListWhere($params);

        // 获取总数
        $total = TrecaShopService::TrecaShopTotal($where);

        // 分页
        $page_params = array(
            'number'    =>  $number,
            'total'     =>  $total,
            'where'     =>  $params,
            'page'      =>  isset($params['page']) ? intval($params['page']) : 1,
            'url'       =>  MyUrl('admin/Trecashop/index'),
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
        $data = TrecaShopService::TrecaShopList($data_params);
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
            $ret = TrecaShopService::TrecaShopList($data_params);
            $data = empty($ret['data'][0]) ? [] : $ret['data'][0];
        }

        // 是否启用
        $this->assign('common_is_enable_list', lang('common_is_enable_list'));

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
        return TrecaShopService::TrecaShopSave($params);
    }

    /**
     * 门店删除
     *
     * @method
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @author: chen
     * @time: 2020/4/9 15:33
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
        return TrecaShopService::TrecaShopDelete($params);
    }

    /**
     * 状态更新
     *
     * @method
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @author: chen
     * @time: 2020/4/9 16:57
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
        return TrecaShopService::TrecaShopStatusUpdate($params);
    }

}