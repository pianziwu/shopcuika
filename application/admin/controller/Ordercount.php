<?php
/**
 * Created by yang
 * User: bonzaphp@gmail.com
 * Date: 2020-04-15
 * Time: 11:31
 */

namespace app\admin\controller;

use app\service\OrderCountService;
use think\Request;

class Ordercount extends Common
{
    /**
     * 订单统计服务
     * @var OrderCountService
     */
    private $order;

    public function __construct()
    {
        parent::__construct();
        $this->order = new OrderCountService();
    }

    /**
     * 订单统计首页
     * @param  Request  $request
     * @return mixed
     * @author bonzaphp@gmail.com
     */
    public function index(Request $request)
    {
        $get_params = $request->get();
        $validate = new \app\admin\validate\OrderCount();
        if (!$validate->scene('index')->check($get_params)) {
            return json(['code' => 400, 'msg' => $validate->getError()]);
        }
        $time = $request->time();
        //会员排行
        $start_time = isset($get_params['start_time']) ? strtotime($get_params['start_time']) : $time - 3600 * 24 * 7;
        $end_time = isset($get_params['end_time']) ? strtotime($get_params['end_time']) : $time;
        if ($start_time > $end_time) {
            throw new \RuntimeException('开始时间必须小于结束时间');
        }
        $first_month = isset($get_params['first_month']) ? strtotime($get_params['first_month']) : null;
        $second_month = isset($get_params['second_month']) ? strtotime($get_params['second_month']) : null;
        $third_month = isset($get_params['third_month']) ? strtotime($get_params['third_month']) : null;
        $forth_month = isset($get_params['forth_month']) ? strtotime($get_params['forth_month']) : null;
        $five_month = isset($get_params['five_month']) ? strtotime($get_params['five_month']) : null;
        $sale_count = $this->order->saleCount($start_time, $end_time);
        $valid_order_count = $this->order->validOrderCount($start_time, $end_time);
        $access_count = $this->order->accessCount($start_time, $end_time);
        $order_per_thousand = $this->order->orderPerThousand($start_time, $end_time);
        $order_per_thousand_price_count = $this->order->orderPerThousandPriceCount($start_time, $end_time);
        $pay_count = $this->order->pay($start_time, $end_time);
        $order_address = $this->order->address($start_time, $end_time);
        $shipping = $this->order->shipping($start_time, $end_time);
        $order_info = $this->order->orderInfo($start_time, $end_time);
        $months = [
            date('Y-m', $first_month)  => $first_month,
            date('Y-m', $second_month) => $second_month,
            date('Y-m', $third_month)  => $third_month,
            date('Y-m', $forth_month)  => $forth_month,
            date('Y-m', $five_month)   => $five_month
        ];
        $months = array_filter($months);
        if (!empty($months)) {
            $month_data = $this->order->getOrderInfoByMonth($months);
            $this->assign(compact('month_data'));
        }
        $this->assign(
            compact(
                'sale_count',
                'valid_order_count',
                'access_count',
                'order_per_thousand',
                'order_per_thousand_price_count',
                'pay_count',
                'order_address',
                'shipping',
                'order_info'
            )
        );
        return $this->fetch('order_count/index');
    }


}