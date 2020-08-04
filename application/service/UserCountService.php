<?php
/**
 * Created by yang
 * User: bonzaphp@gmail.com
 * Date: 2020-03-24
 * Time: 10:48
 */

namespace app\service;

use app\common\model\User;
use RuntimeException;
use think\exception\DbException;

class UserCountService extends User
{
    /**
     * 会员排行
     * 搜索某个时间段的会员消费统计排行榜。
     * 统计的订单都是会员确认收货后的订单。
     * @param  int  $start
     * @param  int  $end
     * @return array
     * @author bonzaphp@gmail.com
     */
    public function memberRank(int $start, int $end): array
    {
        try {
            return User::fieldRaw('username,pay_price,COUNT(qo.id) as count')
                ->whereBetween('pay_time', "{$start},{$end}")
                ->alias('qu')
                ->join('order qo', 'qu.id = qo.user_id')
                ->group(['username', 'pay_price'])
                ->paginate()
                ->toArray();
        } catch (DbException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    public function export()
    {

    }

    /**
     * 会员总数
     * @param  int  $start_time
     * @param  int  $end_time
     * @return int
     * @author bonzaphp@gmail.com
     */
    public function userCount(int $start_time, int $end_time): int
    {
        $c = User::whereBetweenTime('add_time',$start_time,$end_time)->count('*');
        if (is_float($c) || is_string($c)) {
            throw new RuntimeException('统计会员人数失败');
        }
        $c = (int) $c;
        return $c;
    }

    /**
     * 有订单会员数
     * @param  int  $start_time
     * @param  int  $end_time
     * @return int
     * @author bonzaphp@gmail.com
     */
    public function hasOrderUserCount(int $start_time, int $end_time): int
    {
        $c = User::fieldRaw('qu.id')
            ->whereBetweenTime('qu.add_time',$start_time,$end_time)
            ->alias('qu')
            ->join('order qo', 'qu.id = qo.user_id')
            ->group('qu.id')
            ->count();
        return $c;
    }

    /**
     * 会员订单总数
     * @param  int  $start_time
     * @param  int  $end_time
     * @return int
     * @author bonzaphp@gmail.com
     */
    public function userOrderCount(int $start_time, int $end_time): int
    {
        $c = User::fieldRaw('qo.id')
            ->whereBetweenTime('qu.add_time',$start_time,$end_time)
            ->alias('qu')
            ->join('order qo', 'qu.id = qo.user_id')
            ->count();
        return $c;
    }

    /**
     * 会员购物总额
     * @param  int  $start_time
     * @param  int  $end_time
     * @return float
     * @author bonzaphp@gmail.com
     */
    public function priceCount(int $start_time, int $end_time): float
    {
        $c = User::field('qo.pay_price,qo.add_time')
            ->alias('qu')
            /*这里使用订单创建时间，而不是用户创建时间,如果有别名，无法使用whereBetweenTime*/
            ->where('qo.add_time','between',[$start_time,$end_time])
            ->join('qx_order qo', 'qu.id = qo.user_id')
            ->sum('qo.pay_price');
        return sprintf('%.2f',$c);
    }

    /**
     * 统计会员和订单各数据
     * @param  int  $start_time
     * @param  int  $end_time
     * @return array
     * @author bonzaphp@gmail.com
     */
    public function dataInfoPercentage(int $start_time, int $end_time): array
    {
        /* @var $userCount int 会员数 */
        $userCount = $this->userCount($start_time, $end_time);
        /* @var $hasOrderUserCount int 有订单会员总数 */
        $hasOrderUserCount = $this->hasOrderUserCount($start_time, $end_time);
        /* @var $userOrderCount int 会员订单总数 */
        $userOrderCount = $this->userOrderCount($start_time, $end_time);
        /* @var $priceCount float 会员购物总额 */
        $priceCount = $this->priceCount($start_time, $end_time);
        $list = [];
        if ($userCount === 0) {
            $list['purchase_rate'] = 0;
            $list['each_member_order'] = 0;
            $list['each_member_price_count'] = 0;
        } else {
            /* 会员购买率 =  有订单会员数  ÷ 会员总数 */
            $list['purchase_rate'] = sprintf('%.2f',$hasOrderUserCount / $userCount * 100);
            /*每会员订单数 = 会员订单总数 ÷ 会员总数 */
            $list['each_member_order'] = sprintf('%.2f',$userOrderCount / $userCount);
            /*每会员购物总额 = 会员购物总额 ÷ 会员总数 */
            $list['each_member_price_count'] = sprintf('%.2f',$priceCount / $userCount);
        }
        return $list;
    }

}