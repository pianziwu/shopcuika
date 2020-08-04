<?php
/**
 * Created by yang
 * User: bonzaphp@gmail.com
 * Date: 2020-04-15
 * Time: 11:33
 */

namespace app\service;

use app\common\model\Goods;
use app\common\model\Order;
use app\common\model\OrderAddress;
use app\common\model\Payment;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\DbException;

/**
 * 订单统计
 *
 * 统计商城所有的订单，销售总额、有效金额、总点击数、每日点击数、每日点击购买额。
 * 选择不同月份统计出现几个月的订单概括、配送方式、支付方式统计图。
 * Class OrderCountService
 * @author bonzaphp@gmail.com
 * @Date 2020-04-15 14:13
 * @package app\service
 */
class OrderCountService
{
    /**
     * 销售总额
     * @param  int  $start_time
     * @param  int  $end_time
     * @return float
     * @author bonzaphp@gmail.com
     */
    public function saleCount(int $start_time, int $end_time): float
    {
        /*只计算支付状态为已支付的订单*/
        $sum = (float) Order::where('pay_status', 1)
            ->whereBetweenTime('add_time', $start_time, $end_time)
            ->sum('total_price');
        return sprintf('%.2f', $sum);
    }

    /**
     * 有效订单数
     * @param  int  $start_time
     * @param  int  $end_time
     * @return int
     * @author bonzaphp@gmail.com
     */
    public function validOrderCount(int $start_time, int $end_time): int
    {
        /**
         * 有效订单就是支付状态为已支付1，并且订单状态为已支付4的订单
         */
        $condition = [
            ['pay_status', '=', 1],
            ['status', '=', 4],
        ];
        return (int) Order::where($condition)
            ->whereBetweenTime('add_time', $start_time, $end_time)
            ->count();
    }

    /**
     * 总访问数
     * @param  int  $start_time
     * @param  int  $end_time
     * @return int
     * @author bonzaphp@gmail.com
     */
    public function accessCount(int $start_time, int $end_time): int
    {
        return (int) Goods::whereBetweenTime('add_time', $start_time, $end_time)
            ->sum('access_count');
    }

    /**
     * 每千次访问订单数
     * @param  int  $start_time
     * @param  int  $end_time
     * @return float
     * @author bonzaphp@gmail.com
     */
    public function orderPerThousand(int $start_time, int $end_time): float
    {
        /**
         * 实现算法
         * 1，求出所有访问总数
         * 2，求出所有订单数，包括未支付订单和已完成订单
         * 3，订单总数 / 访问总数 * 1000
         */
        $access_count = $this->accessCount($start_time, $end_time);
        $order_count = Order::whereBetweenTime('add_time', $start_time, $end_time)
            ->count('*');
        if ($access_count === 0 ){
            return 0.00;
        }
        return sprintf('%.2f', (float) $order_count / $access_count * 1000);
    }

    /**
     * 每千次访问购物额
     * @param  int  $start_time
     * @param  int  $end_time
     * @return float
     * @author bonzaphp@gmail.com
     */
    public function orderPerThousandPriceCount(int $start_time, int $end_time): float
    {
        /**
         * 实现算法
         * 1，求出订单总价
         * 2，求出访问总数
         * 3，订单总价 / 访问总数 * 1000
         */
        $sale_count = $this->saleCount($start_time, $end_time);
        $access_count = $this->accessCount($start_time, $end_time);
        if ($access_count === 0){
            return 0.00;
        }
        return sprintf('%.2f',$sale_count / $access_count * 1000);
    }

    /**
     * 配送地区统计
     * @param  int  $start_time
     * @param  int  $end_time
     * @return array
     * @author bonzaphp@gmail.com
     */
    public function address(int $start_time, int $end_time): array
    {
        try {
            return OrderAddress::where([])
                ->fieldRaw("count('province') as province_count,qr.name")
                ->whereBetweenTime('qoa.add_time', $start_time, $end_time)
                ->alias('qoa')
                ->join('qx_region qr', 'qr.id = qoa.province')
                ->group('qr.name')
                ->select()
                ->toArray();
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }

    /**
     * 支付方式统计
     * @param  int  $start_time
     * @param  int  $end_time
     * @return array
     * @author bonzaphp@gmail.com
     */
    public function pay(int $start_time, int $end_time): array
    {
        try {
            $pay_count = Order::fieldRaw('qp.name,count(qo.id)')
                ->whereBetweenTime('qo.add_time', $start_time, $end_time)
                ->alias('qo')
                ->join('payment qp', 'qo.payment_id = qp.id')
                ->group('qp.name')
                ->select()
                ->toArray();
            return $pay_count;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }

    /**
     * 配送方式统计
     * @param  int  $start_time
     * @param  int  $end_time
     * @return array
     * @author bonzaphp@gmail.com
     */
    public function shipping(int $start_time, int $end_time): array
    {
        try {
            $express_count = Order::fieldRaw('qe.name,count(qo.id) as shipping_count')
                ->whereBetweenTime('qo.add_time', $start_time, $end_time)
                ->alias('qo')
                ->join('express qe', 'qo.express_id = qe.id')
                ->group('qe.name')
                ->select()
                ->toArray();
            return $express_count;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }

    /**
     * 订单概况-首次进入
     * @param  int  $start_time
     * @param  int  $end_time
     * @return array
     * @author bonzaphp@gmail.com
     */
    public function orderInfo(int $start_time, int $end_time): array
    {
        //订单数量
        $orderCount = Order::field("count(*) as order_count,FROM_UNIXTIME(add_time,'%Y-%m-%d') AS day")
            ->whereBetweenTime('add_time', $start_time, $end_time)
            ->group("FROM_UNIXTIME(add_time,'%Y-%m-%d')")
            ->select()
            ->toArray();
        /**
         * 销售金额
         * 统计的是订单的最终成交价格
         */
        $orderPrice = Order::field("sum(total_price) as price_count,FROM_UNIXTIME(add_time,'%Y-%m-%d') AS day")
            ->whereBetweenTime('add_time', $start_time, $end_time)
            ->group("FROM_UNIXTIME(add_time,'%Y-%m-%d')")
            ->select()
            ->toArray();
        return [$orderCount, $orderPrice];
    }

    /**
     * 订单概况--搜索进入
     * @param  int  $start_time
     * @param  int  $end_time
     * @return array
     * @author bonzaphp@gmail.com
     */
    private function orderInfoByMonth(int $start_time, int $end_time): array
    {
        try {
            $order_info = Order::fieldRaw('count(id) as order_count,status,sum(total_price) as price_count')
                ->whereBetweenTime('add_time', $start_time, $end_time)
                ->group('status')
                ->select()
                ->toArray();
            return $order_info;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }


    /**
     * 返回指定时间戳的某月，开始和结束的时间戳
     *
     * @param $time_stamp
     * @return array
     */
    private static function Month($time_stamp): array
    {
        $y = date('Y', $time_stamp);
        $m = date('m', $time_stamp);
        $begin = mktime(0, 0, 0, $m, 1, $y);
        $end = mktime(23, 59, 59, $m, date('t', $begin), $y);
        return [$begin, $end];
    }

    /**
     * 获取特定月数组中，每月的订单情况
     * @param  array  $months
     * @return array
     * @author bonzaphp@gmail.com
     */
    public function getOrderInfoByMonth(array $months): array
    {
        $month_data = [];
        foreach ($months as $key => $val) {
            $res = self::month($val);
            $month_data[$key] = $this->orderInfoByMonth($res[0], $res[1]);
        }
        /* 返回数据，必须包含7个订单状态，没有的订单状态转为相应的零值 */
        $status = [0, 1, 2, 3, 4, 5, 6];
        foreach ($month_data as $k => &$v) {
            $month_status = array_column($v, 'status');
            $sub_status = array_diff($status, $month_status);
            foreach ($sub_status as $sv) {
                $v[] = [
                    'order_count' => 0,
                    'price_count' => 0,
                    'status'      => $sv
                ];
            }
        }
        return $month_data;
    }

}