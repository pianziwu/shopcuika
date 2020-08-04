<?php
/**
 *
 * Created by PhpStorm.
 * User: spirit_cjw@163.com
 * Date: 2020/4/7
 * Time: 14:36
 */

namespace app\service;


use think\Db;

class NewsService
{
    /**
     * 资讯列表
     *
     * @method
     * @param array $params
     * @param array $order
     * @return array
     * @throws \think\exception\DbException
     * @author: chen
     * @time: 2020/4/7 14:37
     */
    public static function ArticleList(array $params = [], array $order = ['a.id' => 'desc'])
    {
        $where = empty($params['where']) ? [] : $params['where'];
        $field = empty($params['field']) ? 'a.*' : $params['field'];
        $n     = isset($params['n']) ? intval($params['n']) : 10;

        $list = Db::name('Article')
            ->alias('a')
            ->join(['__ARTICLE_CATEGORY__' => 'ac'], 'a.article_category_id=ac.id')
            ->field($field)
            ->where($where)
            ->order($order)
            ->paginate($n, false, [
                'query' => ['cat_id' => input('cat_id')]
            ]);
        $data = $list->toArray();
        if (!empty($data['data'])) {
            $common_is_enable_tips = lang('common_is_enable_tips');
            foreach ($data['data'] as &$v) {
                // url
                $v['url'] = '/news/' . $v['id'];

                // 分类名称
                if (isset($v['article_category_id'])) {
                    $v['article_category_name'] = Db::name('ArticleCategory')->where(['id' => $v['article_category_id']])->value('name');
                }

                // 是否启用
                if (isset($v['is_enable'])) {
                    $v['is_enable_text'] = $common_is_enable_tips[$v['is_enable']]['name'];
                }

                // 内容
                if (isset($v['content'])) {
                    $v['content'] = ResourcesService::ContentStaticReplace($v['content'], 'get');
                }

                // 图片
                if (isset($v['images'])) {
                    if (!empty($v['images'])) {
                        $images = json_decode($v['images'], true);
                        foreach ($images as &$img) {
                            $img = ResourcesService::AttachmentPathViewHandle($img);
                        }
                        $v['images'] = $images;
                    }
                }

                if (isset($v['add_time'])) {
                    $v['add_time_time']  = date('Y-m-d H:i:s', $v['add_time']);
                    $v['add_time_date']  = date('Y-m-d', $v['add_time']);
                    $v['add_time_year']  = date('Y', $v['add_time']);
                    $v['add_time_month'] = date('m/d', $v['add_time']);
                }
                if (isset($v['upd_time'])) {
                    $v['upd_time_time'] = date('Y-m-d H:i:s', $v['upd_time']);
                    $v['upd_time_date'] = date('Y-m-d', $v['upd_time']);
                }
            }
        }
        $res = [
            'page' => $list->render(),
            'data' => $data
        ];
        return DataReturn('处理成功', 0, $res);
    }

    /**
     * 分类
     *
     * @method
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author: chen
     * @time: 2020/4/7 14:55
     */
    public static function ArticleCategoryListContent()
    {
        $data = Db::name('ArticleCategory')->field('id,name')->where(['is_enable' => 1])->order('id asc, sort asc')->select();
        if (!empty($data)) {
            foreach ($data as &$val) {
                $val['url'] = '/news?cat_id=' . $val['id'];
            }
        }
        return DataReturn('处理成功', 0, $data);
    }

    /**
     * 获取上一篇，下一篇
     *
     * @method
     * @param int $id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author: chen
     * @time: 2020/4/8 10:16
     */
    public static function LastAndNext(int $id = 0)
    {
        $last = Db::name('Article')
            ->field(['id', 'title'])
            ->where([
                ['id', '<', $id]
            ])
            ->order(['id' => 'desc'])
            ->findOrEmpty();
        $next = Db::name('Article')
            ->field(['id', 'title'])
            ->where([
                ['id', '>', $id]
            ])
            ->order(['id' => 'asc'])
            ->findOrEmpty();
        if (!empty($last)) {
            $last['url'] = '/news/' . $last['id'];
        }
        if (!empty($next)) {
            $next['url'] = '/news/' . $next['id'];
        }
        return compact('last', 'next');
    }

}