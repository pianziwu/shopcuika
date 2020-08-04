<?php
/**
 * Created by yang
 * User: bonzaphp@gmail.com
 * Date: 2020-05-19
 * Time: 16:56
 */

namespace app\cuika\controller;

use app\service\NewsService;
use base\Page;

class Books extends Common
{
    public function index()
    {
        // 参数
        $get = $_GET;
        // 获取分类和文字
        $article_category_content = NewsService::ArticleCategoryListContent();
        foreach ($article_category_content['data'] as $k =>$v){
            if (is_string($v['name']) && stripos($v['name'],'画册') ){
                $cat_id = $v['id'];
            }
        }
//        $cat_id = $article_category_content['data'][4]['id'];
        // 获取相关文章列表
        $where = [
            ['a.article_category_id', 'eq', $cat_id]
        ];
        $like_params = array(
            'n'     => 3,
            'where' => $where,
            'field' => 'a.id, a.title, a.add_time, a.head_img,a.jump_url',
        );
        $like_list = NewsService::ArticleList($like_params);

        // 分页条数
        $limit = MyC('admin_page_number', 10, true);
        // 分页
        $page_params = array(
            'number'    =>  $limit,
            'total'     =>  $like_list['data']['data']['total'],
            'where'     =>  $get,
            'page'      =>  isset($get['page']) ? (int) $get['page'] : 1,
            'url'       =>  MyUrl('books'),
        );
        $page = new Page($page_params);
        $this->assign('page_html', $page->GetPageHtml());

        $this->assign('like_list', $like_list['data']['data']);
//        die(dump($like_list['data']['data']['data']));
        $this->assign('home_seo_site_title', '百年崔卡');
        return $this->fetch('books/books');
    }
}