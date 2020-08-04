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
 * 商品属性
 * @author   Devil
 * @blog     http://1000-x.cn/
 * @version  0.0.1
 * @datetime 2020-04-01T21:51:08+0800
 */
class GoodsAttributeJoin
{

    /**
     * 获取一条数据
     * @param $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getFind(array $where)
    {
        $list = Db::name('goods_attribute_join')->where($where)->findOrEmpty();
        return !empty($list) ? $list : [];
    }

}
