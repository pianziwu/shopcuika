<?php
/**
 * Created by yang
 * User: bonzaphp@gmail.com
 * Date: 2020-04-01
 * Time: 14:00
 */

namespace app\cuika\controller;

use app\common\model\Favor;
use app\service\BuyService;
use app\service\GoodsDetail;
use app\service\GoodsService;
use think\Db;

/**
 * 产品控制器
 * Class Product
 * @author bonzaphp@gmail.com
 * @Date 2020-04-01 14:23
 * @package app\cuika\controller
 */
class Product extends Common
{
    //床垫
    public function mattress()
    {
        return $this->fetch('product-cd-index');
    }

    //床架
    public function bedstead()
    {
        return $this->fetch('product-cj-index');
    }

    //床架
    public function accessories()
    {
        return $this->fetch('product-pj-index');
    }

    //匠心
    public function originality()
    {
        return $this->fetch('product-jx');
    }

    /**
     * 商品详细
     * @param int $goods_id
     * @return mixed
     */
    public function Detail(int $goods_id)
    {
        try {
            $data_list = GoodsDetail::detail($goods_id);// 商品数据
            if ($data_list) {
                /*未登录情况下，收藏状态默认为未收藏*/
                $data_list['is_favor'] = 0;

                if (!empty($this->user)) {
                    /*检查商品是否在收藏列表*/
                    $favor = Favor::where([
                        ['user_id', '=', $this->user['id']],
                        ['goods_id', '=', $goods_id],
                    ])->findOrEmpty();
                    // if ($favor->isEmpty()) {
                    //     $data_list['is_favor'] = 0;
                    // } else {
                    //     $data_list['is_favor'] = 1;
                    // }
                }
                // seo 信息
                $this->assign('home_seo_site_title', !empty($data_list['seo_title']) ? $data_list['seo_title']: $data_list['title']);
                $this->assign('home_seo_site_keywords', $data_list['seo_keywords'] ?? '');
                $this->assign('home_seo_site_description', $data_list['seo_desc'] ?? '');
            }

            // 详情数据
            $this->assign('data_list', $data_list);
            // 优选推荐
            $this->assign('recommend_list', GoodsDetail::recommend());


            return $this->fetch();
        } catch (\Exception $e) {
            $this->assign('msg', $e->getMessage());
            return $this->fetch('public/tips_error');
        }
    }

    /**
     * 规格页面
     * @author   Devil
     * @blog     http://1000-x.cn/
     * @version  1.0.0
     * @datetime 2018-12-02T23:42:49+0800
     */
    public function Option()
    {
        $goods_id = input('goods_id');

        // 购物车
        $cart_list = BuyService::CartList(['user'=>$this->user]);
        $this->assign('cart_list', $cart_list['data']);

        // 商品
        $data_list = GoodsDetail::option($goods_id);
        $this->assign('data_list', $data_list);

        // 获取规格
        $this->assign('specifications', GoodsService::GoodsSpecifications(['goods_id' => $goods_id]));

        if($data_list){
            // SEO 信息
            $this->assign('home_seo_site_title', !empty($data_list['goods']['seo_title']) ? $data_list['goods']['seo_title'] : $data_list['goods']['title']);
            $this->assign('home_seo_site_keywords', $data_list['goods']['seo_keywords'] ?? '');
            $this->assign('home_seo_site_description', $data_list['goods']['seo_desc'] ?? '');
        }
        return $this->fetch();
    }


    /**
     * 商品规格信息
     * @author   Devil
     * @blog    http://1000-x.cn/
     * @version 1.0.0
     * @date    2018-12-14
     * @desc    description
     */
    public function SpecType()
    {
        // 是否ajax请求
        if(!IS_AJAX)
        {
            return $this->error('非法访问');
        }

        // 开始处理
        $params = input('post.');
        return GoodsService::GoodsSpecType($params);
    }

    /**
     * 商品规格信息
     * @author   Devil
     * @blog    http://1000-x.cn/
     * @version 1.0.0
     * @date    2018-12-14
     * @desc    description
     */
    public function SpecDetail()
    {
        // 是否ajax请求
        if(!IS_AJAX)
        {
            return $this->error('非法访问');
        }

        // 开始处理
        $params = input('post.');
        return GoodsService::GoodsSpecDetail($params);
    }

    /**
     * 搜索
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function Search()
    {
        $get = $this->request->get();

        if (isset($get['treca_keyword']) && $get['treca_keyword']) {
            $where[] = ['title', 'LIKE', "%" . $get['treca_keyword'] . "%"];
        }
        $field = ['id', 'brand_id', 'title', 'images', 'price',];

        // 是否上架is_shelves 1上架0下架
        $where[] = ['is_shelves', '=', 1];
        $where[] = ['is_delete_time', '=', 0];
        // 获取商品信息
        $dataList = Db::name('goods')->field($field)->where($where)->paginate(50);

        $list = !$dataList->isEmpty() ? $dataList->toArray() : [];

        //商品数据
        $this->assign('data_list', $list);
        // SEO
        $this->assign('seo', ['seo_title' => sprintf("%s - 商品搜索 - 崔卡", $get['treca_keyword'])]);
        return $this->fetch();
    }
}
