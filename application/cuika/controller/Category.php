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
namespace app\cuika\controller;

use app\service\CategoryService;
use app\service\GoodsCategoryService;

/**
 * 商品分类
 * @author   Devil
 * @blog     http://1000-x.cn/
 * @version  0.0.1
 * @datetime 2016-12-01T21:51:08+0800
 */
class Category extends Common
{
    protected $service;
    protected $data_series;
    protected $data_list;
    protected $good_category;

    /**
     * 构造方法
     * @author   Devil
     * @blog    http://1000-x.cn/
     * @version 1.0.0
     * @date    2018-11-30
     * @desc    description
     */
    public function __construct()
    {
        parent::__construct();
        $this->service = new CategoryService();
        $this->good_category = new GoodsCategoryService();

        // 左侧分类列表
        $this->data_series = $this->good_category->getSeriesByChildId(input('id'));
        $this->assign('category', $this->data_series);
    }

    /**
     * 首页
     * @author   Devil
     * @blog     http://1000-x.cn/
     * @version  0.0.1
     * @datetime 2017-02-22T16:50:32+0800
     */
    public function Index()
    {
        $category_id = input('id');

        $cateInfo = $this->service->getFind(['id' => $category_id]);

        $data_list = [];
        $fetch = 'first-category';
        if ($cateInfo) {
            // SEO 信息
            $this->assign('home_seo_site_title', empty($cateInfo['seo_title']) ? $cateInfo['name'] : $cateInfo['seo_title']);
            $this->assign('home_seo_site_keywords', $cateInfo['seo_keywords'] ?? '');
            $this->assign('home_seo_site_description', $cateInfo['seo_desc'] ?? '');

            // 一级分类内容列表
            if ($cateInfo['pid'] == 0) {
                $data_list = $this->service->getCategoryChildAndGoodsByCategoryId($category_id);

                $cateInfo['container'] = json_decode($cateInfo['container'], true);
                $this->assign('current_category', $cateInfo);
                $fetch = 'first-category';

            } else {
                // 二级分类内容列表
                $fetch = 'secondary-category';
                $data_list = $this->service->getCategoryAndGoodsByCategoryId($category_id);
            }
        }
        // 数据
        $this->assign('data_list', $data_list);
        return $this->fetch($fetch);
    }
}
