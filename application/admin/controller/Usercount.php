<?php
/**
 * Created by yang
 * User: bonzaphp@gmail.com
 * Date: 2020-03-24
 * Time: 10:45
 */

namespace app\admin\controller;

use app\service\UserCountService;
use base\Page;
use think\Request;

/**
 * 数据分析-会员排行
 * Class UserCount
 * @author bonzaphp@gmail.com
 * @Date 2020-04-14 11:46
 * @package app\admin\controller
 */
class Usercount extends Common
{
    private $user_count;

    public function __construct()
    {
        parent::__construct();
        // 登录校验
        $this->IsLogin();
        // 权限校验
        $this->IsPower();
        $this->user_count = new UserCountService();
    }

    /**
     * 首页
     * @param  Request  $request
     * @return mixed
     * @author bonzaphp@gmail.com
     */
    public function index(Request $request)
    {
        $get_params = $request->get();
        $validate = new \app\admin\validate\UserCount();
        if (!$validate->scene('index')->check($get_params)) {
            return json(['code' => 400, 'msg' => $validate->getError()]);
        }
        $time = $request->time();

        // 分页条数
        $limit = MyC('admin_page_number', 10, true);

        //会员排行
        $start_time = isset($get_params['start_time'])?strtotime($get_params['start_time']):$time - 3600 * 24 * 7;
        $end_time = isset($get_params['end_time']) ?strtotime($get_params['end_time']): $time;
        $member_list = $this->user_count->memberRank($start_time, $end_time);
        $start_time = date('Y-m-d H:i:s',$start_time);
        $end_time = date('Y-m-d H:i:s',$end_time);


        // 分页
        $page_params = [
            'number'    =>  $limit,
            'total'     =>  $member_list['total'],
            'where'     =>  $get_params,
            'page'      =>  isset($get_params['page']) ? (int) $get_params['page'] : 1,
            'url'       =>  MyUrl('admin/Usercount/index'),
        ];
        $page = new Page($page_params);
        $this->assign('page_html', $page->GetPageHtml());

        $this->assign(compact('start_time','end_time'));
        $this->assign('member_list',$member_list);
        return $this->fetch('user_count/index');
    }

    /**
     * 客户统计
     * @param  Request  $request
     * @return mixed|\think\response\Json
     * @author bonzaphp@gmail.com
     */
    public function total(Request $request)
    {
        /**
         * 统计商城所有会员消费记录比例。
         * 会员购买率、平均会员订单数、评价会员购物额、匿名会员评价订单额。
         */

        $get_params = $request->get();
        $validate = new \app\admin\validate\UserCount();
        if (!$validate->scene('index')->check($get_params)) {
            return json(['code' => 400, 'msg' => $validate->getError()]);
        }
        $time = $request->time();

        //会员排行
        $start_time = isset($get_params['start_time'])?strtotime($get_params['start_time']):$time - 3600 * 24 * 7;
        $end_time = isset($get_params['end_time']) ?strtotime($get_params['end_time']): $time;

        $user_count = $this->user_count->userCount($start_time, $end_time);//会员总数
        $has_order_user = $this->user_count->hasOrderUserCount($start_time, $end_time);//有订单会员数
        $user_order_count = $this->user_count->userOrderCount($start_time, $end_time);//用户订单统计
        $price_count = $this->user_count->priceCount($start_time, $end_time);//会员购物总额
        $data_info = $this->user_count->dataInfoPercentage($start_time, $end_time);//会员数据计算
        $start_time = date('Y-m-d H:i:s',$start_time);
        $end_time = date('Y-m-d H:i:s',$end_time);
        $this->assign(compact('user_count','has_order_user', 'user_order_count', 'price_count', 'data_info','start_time','end_time'));
        return $this->fetch('user_count/total');
    }

    /**
     * 导出会员排行
     * @author bonzaphp@gmail.com
     */
    public function exportMemberRank()
    {
        $data = $this->user_count->memberRank();
        $this->user_count->export();
    }


}