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
class GoodsDetail
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
        $list = Db::name('goods_detail')->where($where)->findOrEmpty();
        return !empty($list) ? $list : [];
    }

    /**
     * 获取所有数据
     * @param array $where
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getListAll(array $where)
    {
        $list = Db::name('goods_detail')->where($where)->select();
        return !empty($list) ? $list : [];
    }

    /**
     * 商品详情
     * @param  int  $goods_id
     * @return array
     */
    public static function detail(int $goods_id)
    {
        try {
            $where = ['goods_id' => $goods_id];// 商品数据
            $goodsData = Db::name('Goods')->findOrEmpty($goods_id);
            if (!empty($goodsData)) {
                $web_bottom_param = json_decode($goodsData['web_bottom_params'], true);
                // 底部参数
                $goodsData['web_bottom_params'] = [
                    'left'  => isset($web_bottom_param['left']) ? explode("\n", $web_bottom_param['left']) : '',
                    'right' => isset($web_bottom_param['right']) ? explode("\n", $web_bottom_param['right']) : '',
                ];
                // 内容描述
                $detailData = Db::name('goodsDetail')->where($where)->order('sort asc')->select();
                $goodsData ['goods_detail'] = !empty($detailData) ? $detailData : [];
                // 属性
                $attributeData = Db::name('goodsAttributeJoin')->where($where)->findOrEmpty();
                $goodsData ['goods_attribute_join'] = !empty($attributeData) ? $attributeData : [];
                return $goodsData;
            }
            return [];
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }

    /**
     * 商品规格
     * @param $goods_id
     * @return array|\PDOStatement|string|\think\Model
     */
    public static function option($goods_id){
        try {
            $list = [];
            $goodsData = Db::name('Goods')->findOrEmpty($goods_id);
            if (!empty($goodsData)) {
                $list['goods'] = $goodsData;
                $list['photo'] = [];

                // 获取相册
                if ($goods_id) {
                    $photo = Db::name('GoodsPhoto')->where(['goods_id' => $goods_id, 'is_show' => 1])->order('sort asc')->select();
                    if (!empty($photo)) {
                        foreach ($photo as &$vs) {
                            $vs['images_old'] = $vs['images'];
                            $vs['images'] = ResourcesService::AttachmentPathViewHandle($vs['images']);
                        }
                    }
                    $list['photo'] = $photo;
                }

                return $list;
            }

            return [];
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }

    /**
     * 优选推荐产品
     * @param int $limit
     * @return mixed
     */
    public static function recommend($limit = 3)
    {
        $rangeGoods = Db::query("
            SELECT id, title, price, home_recommended_images
            FROM qx_goods
            WHERE is_shelves = 1 AND is_home_recommended = 1 AND is_delete_time = 0
            ORDER BY rand() LIMIT {$limit}
            ");
        return $rangeGoods;
    }

}
