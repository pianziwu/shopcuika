<?php
// +----------------------------------------------------------------------
// | ShopXO 国内领先企业级B2C免费开源电商系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011~2019 http://1000-x.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Devil
// +----------------------------------------------------------------------
namespace app\service;

use think\Db;

/**
 * 销售明细
 * @author   Devil
 * @blog     http://1000-x.cn/
 * @version  0.0.1
 * @date     2019-10-29
 */
class SaleAnalysisService
{

    /**
     * 销售明细
     * @param array $get
     * @return array
     * @throws \think\exception\DbException
     */
    public function detailIndex($get = [])
    {
        // 条件和字段
        $field = [
            'g.title',
            'o.order_no', 'o.status',
            'od.buy_number', 'od.price', 'od.total_price', 'od.add_time',
        ];
        $where = [];
        //  `pay_status` 支付状态（0未支付, 1已支付, 2已退款, 3部分退款）
        if (isset($get['pay_status']) && $get['pay_status']) {
            $where[] = ['o.pay_status', 'in', explode(',', trim($get['pay_status'], ','))];
        }

        // `status` 订单状态（0待确认, 1已确认/待支付, 2已支付/待发货, 3已发货/待收货, 4已完成, 5已取消, 6已关闭）
        if (isset($get['order_status']) && $get['order_status']) {
            $where[] = ['o.status', 'in', explode(',', trim($get['order_status'], ','))];
        }
        //add_time下单时间，delivery_time发货时间，pay_time支付时间
        if (isset($get['start_time']) && isset($get['end_time']) && $get['start_time'] && $get['end_time']) {
            if (isset($get['time_type']) && in_array($get['time_type'], ['delivery_time', 'add_time', 'pay_time'])) {
                $where[] = [sprintf("o.%s", $get['time_type']), 'between', [strtotime($get['start_time']), strtotime($get['end_time'])]];
            }
        }

        // 查询
        $object = Db::name('order_detail')
            ->alias('od')
            ->join('order o', 'o.id=od.order_id')
            ->join('goods g', 'g.id=od.goods_id')
            ->field($field)->where($where);

        if (isset($get['is_export']) && $get['is_export']) {
            // 导出
            if ($list = $object->select()) {
                $list = $this->detailIndexForeach($list);
            }
        } else {
            // 分页列表
            $list = $object->paginate(10);
            if ($list) {
                $list = $list->toArray();
                $list['data'] = $this->detailIndexForeach($list['data']);
            }
        }
        return $list ?? [];
    }

    /**
     * 销售明细 - 遍历
     * @param $list
     * @return mixed
     */
    public function detailIndexForeach($list)
    {
        $status = self::orderStatus();
        foreach ($list as &$value) {
            $value['price'] = sprintf("%.2f", $value['price']);
            $value['total_price'] = sprintf("%.2f", $value['total_price']);
            $value['add_time'] = date('Y-m-d H:i:s', $value['add_time']);
            $value['status'] = $status[0];
        }
        return $list;
    }

    /**
     * 排行
     * @param array $get
     * @return array
     * @throws \think\exception\DbException
     */
    public function top($get = [])
    {
        // 条件和字段
        $field = [
            'g.title',
            'sum(od.buy_number) as buy_number',
            'sum(od.total_price) as total_price',
            'avg(od.price) as avg_price',
            'od.goods_id',
        ];
        //  `pay_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '支付状态（0未支付, 1已支付, 2已退款, 3部分退款）',
        $where[] = ['o.pay_status', '<>', '0'];

        //add_time下单时间
        if(isset($get['start_time']) && isset($get['end_time']) && $get['start_time'] && $get['end_time']){
            $where[] = ['o.add_time', 'between', [strtotime($get['start_time']), strtotime($get['end_time'])]];
        }

        // 查询
        $object = Db::name('order_detail')
            ->alias('od')
            ->join('order o', 'o.id=od.order_id')
            ->join('goods g', 'g.id=od.goods_id')
            ->field($field)->where($where)
            ->group('od.goods_id');

        if (isset($get['is_export']) && $get['is_export']) {
            // 导出
            if ($list = $object->select()) {
                $list = $this->topForeach($list);
            }
        } else {
            // 分页列表
            $list = $object->paginate(10);
            if ($list) {
                $list = $list->toArray();
                $list['data'] = $this->topForeach($list['data']);
            }
        }
        return $list ?? [];
    }

    /**
     * 销售排行 - 遍历
     * @param $list
     * @return mixed
     */
    public function topForeach($list)
    {
        foreach ($list as &$value) {
            $value['avg_price'] = sprintf("%.2f", $value['avg_price']);
            $value['total_price'] = sprintf("%.2f", $value['total_price']);
        }
        return $list;
    }


    /**
     * 概况
     * @param array $get
     * @return array
     * @throws \think\exception\DbException
     */
    public
    function overview($get = [])
    {
        // 条件和字段
        $field = [
            'g.title',
            'gc.name as category_name',
            'od.buy_number', 'od.price', 'od.total_price', 'od.add_time', 'o.status'
        ];
        $where = [];
        //  `pay_status` 支付状态（0未支付, 1已支付, 2已退款, 3部分退款）
        if (isset($get['pay_status']) && $get['pay_status']) {
            $where[] = ['o.pay_status', 'in', explode(',', trim($get['pay_status'], ','))];
        }

        // `status` 订单状态（0待确认, 1已确认/待支付, 2已支付/待发货, 3已发货/待收货, 4已完成, 5已取消, 6已关闭）
        if (isset($get['order_status']) && $get['order_status']) {
            $where[] = ['o.status', 'in', explode(',', trim($get['order_status'], ','))];
        }

        //add_time下单时间，delivery_time发货时间，pay_time支付时间
        if (isset($get['start_time']) && isset($get['end_time']) && $get['start_time'] && $get['end_time']) {
            if (isset($get['time_type']) && in_array($get['time_type'], ['delivery_time', 'add_time', 'pay_time'])) {
                $where[] = [sprintf("o.%s", $get['time_type']), 'between', [strtotime($get['start_time']), strtotime($get['end_time'])]];
            }
        }
        // 查询
        $object = Db::name('order_detail')
            ->alias('od')
            ->join('order o', 'o.id=od.order_id')
            ->join('goods g', 'g.id=od.goods_id')
            ->join('goods_category_join gcj', 'gcj.goods_id=g.id')
            ->join('goods_category gc', 'gc.id=gcj.category_id')
            ->field($field)
            ->where($where);


        if (isset($get['is_export']) && $get['is_export']) {
            // 导出
            if ($list = $object->select()) {
                $list = $this->overviewForeach($list);
            }
        } else {
            // 分页列表
            $list = $object->paginate(10);
            if ($list) {
                $list = $list->toArray();
                $list['data'] = $this->overviewForeach($list['data']);
            }
        }
        return $list ?? [];
    }


    /**
     * 销售明细 - 遍历
     * @param $list
     * @return mixed
     */
    public function overviewForeach($list)
    {
        foreach ($list as &$value) {
            $value['add_time'] = date('Y-m-d H:i:s', $value['add_time']);
            $value['price'] = sprintf("%.2f", $value['price']);
            $value['total_price'] = sprintf("%.2f", $value['total_price']);
        }
        return $list;
    }

    /**
     * 订单状态
     * @return array
     */
    public function orderStatus()
    {
        return [
            '0' => '待确认',
            '1' => '已确认/待支付',
            '2' => '已支付/待发货',
            '3' => '已发货/待收货',
            '4' => '已完成',
            '5' => '已取消',
            '6' => '已关闭',
        ];
    }

    /**
     * 支付状态
     * @return array
     */
    public function payStatus()
    {
        return [
            '0' => '未支付',
            '1' => '已支付',
            '2' => '已退款',
            '3' => '部分退款',
        ];
    }
}
