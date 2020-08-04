<?php
/**
 *
 * Created by PhpStorm.
 * User: spirit_cjw@163.com
 * Date: 2020/4/9
 * Time: 13:34
 */

namespace app\cuika\controller;


use app\service\RecruitService;

class Recruit extends Common
{

    /**
     * 崔佧资讯
     *
     * @method
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author: chen
     * @time: 2020/4/7 14:52
     */
    public function index()
    {
        // 条件
        $where[] = ['is_enable', 'eq', 1];

        // 获取列表
        $data_params = [
            'n'     => 1000,
            'where' => $where,
            'field' => 'id, title, address, add_time, content',
        ];
        $data = RecruitService::RecruitList($data_params);
        $this->assign('data_list', $data['data']);

        $this->assign('home_seo_site_title', '加入我们');
        return $this->fetch('joinUs');
    }
}