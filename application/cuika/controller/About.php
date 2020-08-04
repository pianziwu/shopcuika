<?php
/**
 *
 * Created by PhpStorm.
 * User: spirit_cjw@163.com
 * Date: 2020/4/2
 * Time: 15:28
 */

namespace app\cuika\controller;


use app\service\RegionService;
use app\service\TrecaShopService;

class About extends Common
{
    /**
     * 门店地址
     *
     * @method
     * @return mixed
     * @author: chen
     * @time: 2020/4/10 10:24
     */
    public function address()
    {
        $this->assign('home_seo_site_title', '门店地址');
        return $this->fetch('contactUs');
    }

    /**
     * 招商加盟
     *
     * @method
     * @return mixed
     * @author: chen
     * @time: 2020/4/10 10:24
     */
    public function zsjm()
    {
        $this->assign('home_seo_site_title', '招商加盟');
        return $this->fetch('zsjm');
    }

    /**
     * 售后质保
     *
     * @method
     * @return mixed
     * @author: chen
     * @time: 2020/4/10 10:24
     */
    public function afterSale()
    {
        $this->assign('home_seo_site_title', '售后质保');
        return $this->fetch('aftersale');
    }

    /**
     * 退换货服务
     *
     * @method
     * @return mixed
     * @author: chen
     * @time: 2020/4/10 10:24
     */
    public function alteration()
    {
        $this->assign('home_seo_site_title', '退换货服务');
        return $this->fetch('alteration');
    }

    /**
     * 隐私政策
     *
     * @method
     * @return mixed
     * @author: chen
     * @time: 2020/4/10 10:24
     */
    public function pr()
    {
        $this->assign('home_seo_site_title', '隐私政策');
        return $this->fetch('privacyPolicy');
    }

    /**
     * 换购计划
     *
     * @method
     * @return mixed
     * @author: chen
     * @time: 2020/4/15 18:07
     */
    public function redemption()
    {
        $this->assign('home_seo_site_title', '换购计划');
        return $this->fetch('redemption');
    }

    /**
     * 门店信息
     *
     * @method
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author: chen
     * @time: 2020/4/9 16:07
     */
    public function shop()
    {
        // 是否ajax请求
        if(!IS_AJAX)
        {
            $this->error('非法访问');
        }

        // 参数
        $params = input();

        // 条件
        $where = TrecaShopService::TrecaShopListWhere($params);

        // 获取列表
        $data_params = array(
            'n'     => 1000,
            'where' => $where,
            'field' => '*',
        );
        return TrecaShopService::TrecaShopList($data_params);
    }

    /**
     * 门店省市选择列表
     *
     * @method
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author: chen
     * @time: 2020/4/11 15:09
     */
    public function region()
    {
        // 是否ajax请求
        if(!IS_AJAX)
        {
            $this->error('非法访问');
        }

        $list = TrecaShopService::get_all_shop_region();
        if (empty($list)) {
            return DataReturn('操作成功', 0, []);
        }

        // 获取地区
        $params = [
            'where' => [
                'pid' => (int)(input('pid', 0)),
                'id'  => $list
            ],
        ];
        $data = RegionService::RegionNode($params);
        return DataReturn('操作成功', 0, $data);
    }


}