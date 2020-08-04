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
 * 商品分类
 * @author   Devil
 * @blog     http://1000-x.cn/
 * @version  0.0.1
 * @datetime 2020-04-01T21:51:08+0800
 */
class GoodsCategoryService
{
    /**
     * 根据分类ID获取子分类ID
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
            // 多维数组转一组数组
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
     * 返回商品ID，根据分类ID
     * @param array $categoryIds
     * @return array
     */
    public function getGoodsIdByCategoryId($categoryIds = [])
    {
        $where = ['category_id' => $categoryIds];
        return Db::name('goods_category_join')->where($where)->column('goods_id');
    }

    /**
     * 通过分类ID返回商品列表
     * @param int $pid
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getGoodsInfoByCategoryId($pid = 0)
    {
        // 获取子分类ID
        $cateGoryInfo = $this->GoodsCategoryChildIds($pid);
        $cateGoryInfo[]['id'] = $pid;
        // 获取商品ID
        $goodsId = $this->getGoodsIdByCategoryId(array_column($cateGoryInfo, 'id'));

        $where = [
            'is_shelves' => 1,
            'id' => $goodsId,
        ];
        $field = [
            'id', 'title', 'price', 'images',
            "ga.hardness", "ga.height", "ga.winter_fabrics", "ga.summer_fabrics",
        ];
        // 获取商品信息
        $dataList = Db::name('goods')
            ->alias('g')
            ->leftJoin('goods_attribute_join ga', 'ga.goods_id = g.id')
            ->field($field)
            ->where($where)
            ->paginate();

        if (!$dataList->isEmpty()) {
            return $dataList->toArray();
        }
        return [];
    }

    /**
     * 获取当前分类的同级分类
     * @param $category_id
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getSeriesByChildId($category_id)
    {
        // 获取所有一级分类
        $firstList = Db::name('goods_category')->field(['id', 'icon', 'name'])->where(['pid' => 0, 'is_enable' => 1])->select();

        //获取当前类的同级分类
        $peerList = Db::name('goods_category')
            ->alias('gc1')
            ->join('goods_category gc2', 'gc2.pid = gc1.pid')
            ->field(['gc2.id', 'gc2.pid', 'gc2.name', 'gc2.icon'])
            ->where(['gc1.id' => $category_id, 'gc2.is_enable' => 1])
            ->select();
        return ['first' => $firstList, 'second' => $peerList];
    }

}
