<?php
/**
 * Created by yang
 * User: bonzaphp@gmail.com
 * Date: 2020-04-01
 * Time: 14:32
 */

namespace app\cuika\controller;

use app\service\StylistService;

/**
 * 匠心原创
 * Class originality
 * @author bonzaphp@gmail.com
 * @Date 2020-04-01 14:33
 * @package app\cuika\controller
 */
class Originality extends Common
{

    /**
     * 匠心工艺
     *
     * @method
     * @return mixed
     * @author: chen
     * @time: 2020/4/14 13:40
     */
    public function index()
    {
        $this->assign('home_seo_site_title', '匠心工艺');
        return $this->fetch('jx');
    }

    /**
     * 匠心
     *
     * @method
     * @return mixed
     * @author: chen
     * @time: 2020/4/14 13:40
     */
    public function jx()
    {
        $this->assign('home_seo_site_title', '匠心');
        return $this->fetch('bnjx');
    }

    /**
     * 匠人
     *
     * @method
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author: chen
     * @time: 2020/4/14 13:40
     */
    public function jr()
    {
        // 条件
        $params = [
            'where' => [
                ['is_enable', 'eq', 1]
            ],
            'field' => ['id', 'chinese_name', 'english_name', 'synopsis', 'head_img', 'photo', 'chinese_name_color', 'english_name_color'],
            'n' => 1000,
        ];

        // 大师列表
        $stylist = StylistService::StylistList($params);
        $this->assign('data', $stylist['data']);

        $this->assign('home_seo_site_title', '匠人');
        return $this->fetch('workman');
    }

    /**
     * 珍材
     *
     * @method
     * @return mixed
     * @author: chen
     * @time: 2020/4/14 13:40
     */
    public function zc()
    {
        $this->assign('home_seo_site_title', '珍材');
        return $this->fetch('virginMaterial');
    }

}