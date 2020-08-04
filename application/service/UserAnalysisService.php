<?php
/**
 *
 * Created by PhpStorm.
 * User: spirit_cjw@163.com
 * Date: 2020/3/18
 * Time: 15:57
 */

namespace app\service;


use think\Db;

class UserAnalysisService
{

    /**
     * 新增用户分析
     *
     * @method
     * @param array $where
     * @param array $time_list
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author: chen
     * @time: 2020/3/18 15:36
     */
    public static function add_user(array $where = [], array $time_list = []): array
    {
        $field = ['count(*) as count', 'from_unixtime(add_time, "%Y-%m-%d") as add_time'];
        $list = Db::name('user')->field($field)->where($where)->group('add_time')->order('add_time')->select();
        if ($list) {
            foreach ($list as $key => $value) {
                $time = date('n/j', strtotime($value['add_time']));
                $time_list[$time] = $value['count'];
            }
        }
        return $time_list;
    }

    /**
     * 会员分析
     *
     * @method
     * @param array $where
     * @param int $limit
     * @return array
     * @throws \think\exception\DbException
     * @author: chen
     * @time: 2020/3/21 15:46
     */
    public static function pay_list(array $where = [], int $limit = 10): array
    {
        $field = ['user.id', 'user.username', 'user.mobile', 'SUM(order.pay_price) as pay_total'];
        return Db::name('user')
            ->alias('user')
            ->leftJoin('order order', 'order.user_id = user.id')
            ->field($field)
            ->where($where)
            ->group('user.id')
            ->order(['pay_total' => 'desc'])
            ->paginate($limit)
            ->toArray();
    }

    /**
     * 会员地区分析
     *
     * @method
     * @param array $where
     * @param int $limit
     * @return array
     * @throws \think\exception\DbException
     * @author: chen
     * @time: 2020/3/21 17:00
     */
    public static function area_list(array $where = [], int $limit = 0): array
    {
        $field = [
            'any_value(address.province_name) as province_name',
            'any_value(address.city_name) as city_name',
            'any_value(address.county_name) as county_name',
            'any_value(address.province) as province',
            'any_value(address.city) as city',
            'address.county',
            'COUNT(order.id) as order_count',
            'SUM(order.pay_price) as total_price',
        ];
        $list =  Db::name('order_address')
            ->alias('address')
            ->field($field)
            ->join('order order', 'order.id = address.order_id')
            ->where($where)
            ->group('address.county')
            ->paginate($limit)
            ->toArray();
        if (!empty($list['data'])) {
            foreach ($list['data'] as &$val) {
                $val['user_count'] = Db::name('order_address')
                    ->where(['county' => $val['county']])
                    ->group('user_id')
                    ->count();
            }
        }
        return $list;
    }

}