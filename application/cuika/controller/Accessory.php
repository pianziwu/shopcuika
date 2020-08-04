<?php
/**
 * 配件
 * Created by huang
 * User: 1767474346@qq.com
 * Date: 2020-04-01
 * Time: 14:00
 */

namespace app\cuika\controller;

class Accessory extends Common
{
    protected $good_category = '';
    protected $pillow = 925;
    protected $bedside = 927;
    protected $bedChair = 929;
    protected $data_series = [];
    protected $data_list = [];

    public function __construct()
    {
        parent::__construct();
        $this->good_category = new \app\service\GoodsCategoryService();
        // 系列
        $this->assign('product_series', $this->good_category->getSeriesByChildId(input('category_id')));
        // 参数
        $this->data_list = $this->good_category->getGoodsInfoByCategoryId(input('category_id'));
        $this->assign('data_list', $this->data_list);
    }

    //首页
    public function Index()
    {
        // 参数
        $dataList = [
            'pillow' => $this->good_category->getGoodsInfoByCategoryId($this->pillow),
            'bedside' => $this->good_category->getGoodsInfoByCategoryId($this->bedside),
            'bed_chair' => $this->good_category->getGoodsInfoByCategoryId($this->bedChair),
            'category_id' => ['pillow'=>$this->pillow, 'bedside'=>$this->bedside, 'bed_chair'=>$this->bedChair],
        ];
        $this->assign('data_list', $dataList);
        return $this->fetch('index');
    }

    //枕头系列
    public function Pillow()
    {
        return $this->fetch('pillow');
    }

    //床头柜系列
    public function Bedside()
    {
        return $this->fetch('bedside');
    }

    //床尾凳系列
    public function BedChair()
    {
        return $this->fetch('bed-chair');
    }
}
