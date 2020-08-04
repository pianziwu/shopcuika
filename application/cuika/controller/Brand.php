<?php
/**
 * Created by yang
 * User: bonzaphp@gmail.com
 * Date: 2020-04-06
 * Time: 16:42
 */

namespace app\cuika\controller;


use app\service\ChronicleService;
use app\service\NewsService;

/**
 * 百年崔卡
 * Class Brand
 * @author bonzaphp@gmail.com
 * @Date 2020-04-06 16:44
 * @package app\cuika\controller
 */
class Brand extends Common
{
    /**
     * 百年文化
     *
     * @method
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author: chen
     * @time: 2020/4/15 14:22
     */
    public function index()
    {
        // 获取分类和文字
        $article_category_content = NewsService::ArticleCategoryListContent();
        $cat_id = $article_category_content['data'][0]['id'];
        // 获取相关文章列表
        $where = [
            ['a.article_category_id', 'eq', $cat_id]
        ];
        $like_params = array(
            'n'     => 3,
            'where' => $where,
            'field' => 'a.id, a.title, a.add_time, a.head_img',
        );
        $like_list = NewsService::ArticleList($like_params);
        $this->assign('like_list', $like_list['data']['data']['data']);

        // 大事记
        // 条件
        $chronicle_params_= [
            'is_enable' => 1,
            'is_more' => 1
        ];
        $chronicle_where = ChronicleService::ChronicleListWhere($chronicle_params_);
        // 获取列表
        $chronicle_params = array(
            'n'     => 1000,
            'where' => $chronicle_where,
            'field' => '*',
        );
        $chronicle = ChronicleService::ChronicleList($chronicle_params);
        $this->assign('chronicle', $chronicle['data']);

        $this->assign('home_seo_site_title', '百年崔卡');
        return $this->fetch('brand/story');
    }
}