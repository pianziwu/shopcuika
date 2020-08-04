<?php
/**
 * 床垫
 * Created by huang
 * User: 1767474346@qq.com
 * Date: 2020-04-01
 * Time: 14:00
 */

namespace app\cuika\controller;

use app\service\GoodsCategoryService;

class Mattress extends Common
{
    protected $good_category = '';
    protected $infinite = 905;
    protected $planetCoordinates = 907;
    protected $dreamland = 909;
    protected $mel = 911;
    protected $leixoesSC = 913;
    protected $data_series = [];
    protected $data_list = [];

    public function __construct()
    {
        parent::__construct();
        $this->good_category = new GoodsCategoryService();
        // 系列
        $this->data_series = $this->good_category->getSeriesByChildId(input('category_id'));
        $this->assign('category', $this->data_series);
        // 参数
        $this->data_list = $this->good_category->getGoodsInfoByCategoryId(input('category_id'));
        $this->assign('data_list', $this->data_list);
    }

    /**
     * 首页
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function Index()
    {
        // 参数
        $dataList = [
            'infinite' => $this->good_category->getGoodsInfoByCategoryId($this->infinite),
            'planet_coordinates' => $this->good_category->getGoodsInfoByCategoryId($this->planetCoordinates),
            'dreamland' => $this->good_category->getGoodsInfoByCategoryId($this->dreamland),
            'mel' => $this->good_category->getGoodsInfoByCategoryId($this->mel),
            'leixoes_s_c' => $this->good_category->getGoodsInfoByCategoryId($this->leixoesSC),
            'category_id' => [
                'infinite' => $this->infinite,
                'planet_coordinates' => $this->planetCoordinates,
                'dreamland' => $this->dreamland,
                'mel' => $this->mel,
                'leixoes_s_c' => $this->leixoesSC
            ],
        ];
        $this->assign('data_list', $dataList);
        return $this->fetch();
    }

    //无限系列
    public function Infinite()
    {
        return $this->fetch('infinite');
    }

    //星球坐标系列
    public function PlanetCoordinates()
    {
        return $this->fetch('planet-coordinates');
    }

    //梦境系列
    public function Dreamland()
    {
        return $this->fetch('dreamland');
    }

    //梅尔系列
    public function Mel()
    {
        return $this->fetch('mel');
    }

    //雷克索方系列
    public function LeixoesSC()
    {
        return $this->fetch('leixoes-s-c');
    }

}
