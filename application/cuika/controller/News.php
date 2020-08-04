<?php
/**
 *
 * Created by PhpStorm.
 * User: spirit_cjw@163.com
 * Date: 2020/4/2
 * Time: 17:44
 */

namespace app\cuika\controller;


use app\service\ArticleService;
use app\service\NewsService;

class News extends Common
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
        // 参数
        $params = input();

        // 分页
        $number = MyC('admin_page_number', 10, true);

        // 条件
        $cat_id = $params['cat_id'] ?? 0;

        $where = [];
        $where[] = ['a.is_enable', 'eq', 1];
        if ($cat_id) {
            $where[] = ['a.article_category_id', 'eq', $cat_id];
        }

        // 获取列表
        $data_params = array(
            'n'     => $number,
            'where' => $where,
            'field' => 'a.id, a.title, a.synopsis, a.head_img, a.add_time, ac.name as cat_name',
        );
        $data = NewsService::ArticleList($data_params);
        $this->assign('data_list', $data['data']['data']['data']);
        $this->assign('page_html', $data['data']['page']);

        // 当前选择的分类ID
        $this->assign('current_catId', $cat_id);

        // 获取分类和文字
        $article_category_content = NewsService::ArticleCategoryListContent();
        $this->assign('category_list', $article_category_content['data']);

        $this->assign('home_seo_site_title', '崔佧资讯');
        return $this->fetch('news');
    }

    /**
     * 资讯详情
     *
     * @method
     * @param int $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author: chen
     * @time: 2020/4/8 9:38
     */
    public function read(int $id)
    {
        // 数据
        $data = [];
        if($id)
        {
            // 获取列表
            $data_params = array(
                'm'     => 0,
                'n'     => 1,
                'where' => ['a.id' => (int)$id],
                'field' => 'a.*',
            );
            $ret = ArticleService::ArticleList($data_params);
            $data = empty($ret['data'][0]) ? [] : $ret['data'][0];
        }
        $this->assign('data', $data);

        // 上一篇/下一篇
        $LastAndNext = NewsService::LastAndNext($id);
        $this->assign('last_next', $LastAndNext);

        // 获取相关文章列表
        $cat_id = $data['article_category_id'] ?? 0;
        $where = [
            ['a.id', 'neq', $id]
        ];
        if ($cat_id) {
            $where[] = ['a.article_category_id', 'eq', $cat_id];
        }
        $like_params = array(
            'n'     => 4,
            'where' => $where,
            'field' => 'a.id, a.title, a.head_img',
        );
        $like_list = NewsService::ArticleList($like_params);
        $this->assign('like_list', $like_list['data']['data']['data']);

        // 获取热门文章列表
        $hot_params = array(
            'n'     => 5,
            'where' => [
                ['a.id', 'neq', $id]
            ],
            'field' => 'a.id, a.title',
        );
        $hot_list = NewsService::ArticleList($hot_params, ['a.access_count' => 'desc']);
        $this->assign('hot_list', $hot_list['data']['data']['data']);

        return $this->fetch('newsdetails');
    }

}