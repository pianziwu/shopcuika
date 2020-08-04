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
 * 分类服务层
 * @author   Devil
 * @blog     http://1000-x.cn/
 * @version  0.0.1
 * @datetime 2016-12-01T21:51:08+0800
 */
class CategoryService
{

    public function getFind($where)
    {
        $dataList = Db::name('GoodsCategory')->where($where)->findOrEmpty();
        if ($dataList) {
            return $dataList;
        }
        return [];
    }

    /**
     * 获取子分类
     * @param int $parent_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function GoodsCategoryChildIds($parent_id = 0)
    {
        $field = ['id', 'pid', 'name'];
        $where = ['is_enable' => 1];
        $listData = Db::name('GoodsCategory')->where($where)->field($field)->order('sort asc')->select();
        if ($listData) {
            // 一组数组转多维数组转数组
            $depInfo = static function ($listData, $parent_id) use (&$depInfo) {
                static $arr = [];
                foreach ($listData as $val) {
                    if ($val['pid'] == $parent_id) {
                        $arr [] = ['id' => $val['id'], 'name' => $val['name'], 'pid' => $val['pid']];
                        $depInfo($listData, $val['id']);
                    }
                }
                return $arr;
            };
            return $depInfo($listData, $parent_id);
        }
        return [];
    }

    /**
     * 一级分类展示
     * @param int $category_id
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCategoryChildAndGoodsByCategoryId(int $category_id)
    {
        // 获取子分类信息
        $field = ['*'];
        // is_enable 是否启用 1启用0不启用 ,is_home_recommended 是否首页推荐 1推荐0不推荐
        $where = ['is_enable' => 1, 'pid' => $category_id, 'is_home_recommended' => 1];
        $categoryData = Db::name('GoodsCategory')->where($where)->field($field)->order('sort asc')->select();
        // 获取商品信息
        foreach ($categoryData as &$row) {
            $row['container'] && $row['container'] = json_decode($row['container'], true);
            $row['goods_info'] = $this->getCategoryJoinGoods($row['id']);
        }

        return $categoryData;
    }

    /**
     * 二级分类展示
     * @param int $category_id
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCategoryAndGoodsByCategoryId(int $category_id)
    {
        // 获取子分类信息
        $field = ['*'];
        $where = ['is_enable' => 1, 'id' => $category_id];
        $categoryData = Db::name('GoodsCategory')->where($where)->field($field)->order('sort asc')->find();

        // 获取商品信息、属性
        if ($categoryData) {
            $categoryData['container'] && $categoryData['container'] = json_decode($categoryData['container'], true);
            $categoryData['goods_info'] = $this->getCategoryJoinGoodsJoinAttribute($categoryData['id']);
        }
        return $categoryData;
    }


    /**
     * 通过分类获取商品数据
     * @param int $categoryIds
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCategoryJoinGoods(int $categoryIds)
    {
        $where = [
            'j.category_id' => $categoryIds,
            'g.is_shelves' => 1,
            'g.is_delete_time' => 0,
        ];
        $field = ['g.id', 'g.title', 'g.price', 'g.images'];

        // 获取商品分类
        $dataList = Db::name('GoodsCategoryJoin')
            ->alias('j')
            ->leftJoin('Goods g', 'j.goods_id = g.id')
            ->field($field)
            ->where($where)->select();

        return $dataList;
    }


    /**
     * 通过分类获取商品数据，属性
     * @param int $categoryIds
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCategoryJoinGoodsJoinAttribute(int $categoryIds)
    {
        $where = [
            'j.category_id' => $categoryIds,
            'g.is_shelves' => 1,
            'g.is_delete_time' => 0,
        ];
        $field = ['g.id', 'g.title', 'g.price', 'g.images',
            "ga.hardness", "ga.height", "ga.winter_fabrics", "ga.summer_fabrics",
        ];

        // 获取商品分类
        $dataList = Db::name('GoodsCategoryJoin')
            ->alias('j')
            ->leftJoin('Goods g', 'j.goods_id = g.id')
            ->leftJoin('goods_attribute_join ga', 'ga.goods_id = g.id')
            ->field($field)
            ->where($where)
            ->select();

        return $dataList;
    }

}
