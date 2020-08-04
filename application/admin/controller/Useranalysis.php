<?php
/**
 * 会员分析统计
 *
 * Created by PhpStorm.
 * User: spirit_cjw@163.com
 * Date: 2020/3/18
 * Time: 14:18
 */

namespace app\admin\controller;

use app\service\TimeStamp;
use app\service\UserAnalysisService;
use base\Page;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\DbException;

class Useranalysis extends Common
{
    private $service;

    public function __construct()
    {
        // 调用父类前置方法
        parent::__construct();

        // 登录校验
        $this->IsLogin();

        // 权限校验
        $this->IsPower();

        $this->service = new UserAnalysisService();

    }

    /**
     * 新增会员
     * 统计图可根据所选时间段展示新增会员数量走势。
     *
     * @method GET
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws DbException
     * @author: chen
     * @time: 2020/3/18 15:39
     */
    public function index()
    {
        $get = $_GET;
        $start_time = $get['start_time'] ?? '';
        $end_time = $get['end_time'] ?? '';
        $time_list = TimeStamp::search($start_time, $end_time, 'week');
        $between_time = [strtotime($time_list['format'][0]), strtotime($time_list['format'][1])];
        $where = [
            ['status', 'eq', 0],
            ['is_delete_time', 'eq', 0],
            ['add_time', 'between', $between_time]
        ];
        $list = $this->service::add_user($where, $time_list['coordinates']);
        $this->assign('list', $list);

        // 返回时间参数
        $this->assign('search_time', compact('start_time', 'end_time'));

        return $this->fetch();
    }

    /**
     * 消费分析
     * 有效订单：所选时间段内除无效订单状态下的所有订单总数。
     * 可根据所选时间段统计有效订单的下单量和下单总金额排行。
     *
     * @method GET
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author: chen
     * @time: 2020/3/21 15:43
     */
    public function pay_list()
    {
        // 参数
        $get = $_GET;
        $start_time = $get['start_time'] ?? '';
        $end_time = $get['end_time'] ?? '';

        // 分页条数
        $limit = MyC('admin_page_number', 10, true);

        // where条件
        $where = [
            ['order.status', 'not in', [5, 6]],
        ];
        if ($start_time && $end_time) {
            $where[] = ['order.add_time', 'between', [strtotime($start_time), strtotime($end_time)]];
        }

        $list = $this->service::pay_list($where, $limit);
        $this->assign('list', $list);

        // 分页
        $page_params = array(
            'number'    =>  $limit,
            'total'     =>  $list['total'],
            'where'     =>  $get,
            'page'      =>  isset($get['page']) ? intval($get['page']) : 1,
            'url'       =>  MyUrl('admin/Useranalysis/pay_list'),
        );
        $page = new Page($page_params);
        $this->assign('page_html', $page->GetPageHtml());

        // 返回时间参数
        $this->assign('search_time', compact('start_time', 'end_time'));

        return $this->fetch();
    }

    /**
     * 地域分析
     * 有效订单：所选时间段内除无效订单状态下的所有订单总数。
     * 根据用户地址统计所选时间段内各地区有效订单下单会员总数、下单总金额和下单量。
     *
     * @method GET
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author: chen
     * @time: 2020/3/21 15:43
     */
    public function area_list()
    {
        // 参数
        $get = $_GET;
        $start_time = $get['start_time'] ?? '';
        $end_time = $get['end_time'] ?? '';

        // 分页条数
        $limit = MyC('admin_page_number', 10, true);

        // where条件
        $where = [
            ['order.status', 'not in', [5, 6]],
        ];
        if ($start_time && $end_time) {
            $where[] = ['order.add_time', 'between', [strtotime($start_time), strtotime($end_time)]];
        }

        $list = $this->service::area_list($where, $limit);
        $this->assign('list', $list);

        // 分页
        $page_params = array(
            'number'    =>  $limit,
            'total'     =>  $list['total'],
            'where'     =>  $get,
            'page'      =>  isset($get['page']) ? intval($get['page']) : 1,
            'url'       =>  MyUrl('admin/Useranalysis/area_list'),
        );
        $page = new Page($page_params);
        $this->assign('page_html', $page->GetPageHtml());

        // 返回时间参数
        $this->assign('search_time', compact('start_time', 'end_time'));

        return $this->fetch();
    }

}