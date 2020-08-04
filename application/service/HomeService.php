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
namespace app\service;

use think\Db;

/**
 * 轮播图服务层
 * @author   Devil
 * @blog     http://1000-x.cn/
 * @version  0.0.1
 * @datetime 2016-12-01T21:51:08+0800
 */
class HomeService
{

    public function detail($get)
    {
        if (isset($get['id']) && $get['id']) {
            $dataList = Db::name('Home')->findOrEmpty($get['id']);
            return $dataList;

        }
        return [];

    }

    /**
     * 数据保存
     * @author   Devil
     * @blog    http://1000-x.cn/
     * @version 1.0.0
     * @date    2018-12-19
     * @desc    description
     * @param   [array]          $params [输入参数]
     */
    public static function Save($params = [])
    {
        // '类型 (1 置顶视频, 2 商品, 3 设计师介绍 4 联系方式)'

        // 判断
        if ($params['show_type'] == 1 && $params['video_url'] == '') {
            return DataReturn('视频链接不能空', -1);
        }
        if ($params['show_type'] == 2) {
            $list = Db::name('Goods')->findOrEmpty($params['goods_id']);
            if (!$list) {
                return DataReturn('商品ID错误，商品列表找不到此商品', -1);
            }
        }
        if (in_array($params['show_type'], [2, 3]) && $params['images_url'] == '') {
            return DataReturn('展示图片不能空', -1);
        }

        // 附件
        $data_fields = ['images_url'];
        $attachment = ResourcesService::AttachmentParams($params, $data_fields);

        // 数据
        $data = [
            'title' => $params['title'],
            'show_type' => $params['show_type'],
            'subtitle' => $params['subtitle'],
            'describe' => $params['describe'],
            'video_url' => $params['video_url'],
            'images_url' => $attachment['data']['images_url'],
            'sort' => intval($params['sort']),
            'is_enable' => isset($params['is_enable']) ? intval($params['is_enable']) : 0,
            'goods_id' => isset($params['goods_id']) ? intval($params['goods_id']) : 0,
        ];

        if (empty($params['id'])) {
            $data['add_time'] = time();
            if (Db::name('Home')->insertGetId($data) > 0) {
                return DataReturn('添加成功', 0);
            }
            return DataReturn('添加失败', -100);
        } else {
            $data['upd_time'] = time();
            if (Db::name('Home')->where(['id' => intval($params['id'])])->update($data)) {
                return DataReturn('编辑成功', 0);
            }
            return DataReturn('编辑失败', -100);
        }
    }

    /**
     * 列表
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getList()
    {
        $list = Db::name('Home')->order('sort asc')->select();

        foreach ($list as &$row) {
            $row['show_type_text'] = $this->showType($row['show_type'])['text'];
            $row['is_enable_text'] = $this->isEnable($row['is_enable'])['text'];
            $row['add_time'] = $this->formatTime($row['add_time']);
            $row['upd_time'] = $this->formatTime($row['upd_time']);
        }
        return $list;
    }

    /**
     * 首页列表(前端调用
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function homeIndex()
    {
        $data_list = ['top_info' => [], 'goods_list' => [], 'sty_list' => [], 'contact' => []];
        $list = Db::name('Home')->where(['is_enable' => 1])->order('sort asc')->select();

        foreach ($list as &$row) {
            $row['show_type_text'] = self::showType($row['show_type'])['text'];
            $row['is_enable_text'] = self::isEnable($row['is_enable'])['text'];
            $row['add_time'] = self::formatTime($row['add_time']);
            $row['upd_time'] = self::formatTime($row['upd_time']);

            if ($row['show_type'] == 1) {
                $data_list ['top_info'] = $row;
            } elseif ($row['show_type'] == 2) {
                $data_list['goods_list'][] = $row;
            } elseif ($row['show_type'] == 3) {
                $data_list['sty_list'][] = $row;
            } elseif ($row['show_type'] == 4) {
                $data_list['contact'] = $row;
            }
        }
        return $data_list;
    }


    /**
     * @param array $params
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function Delete($params = [])
    {
        // 请求参数
        $p = [
            [
                'checked_type' => 'empty',
                'key_name' => 'id',
                'error_msg' => '操作id有误',
            ],
        ];
        $ret = ParamsChecked($params, $p);
        if ($ret !== true) {
            return DataReturn($ret, -1);
        }

        // 删除操作
        if (Db::name('Home')->where(['id' => $params['id']])->delete()) {
            return DataReturn('删除成功');
        }

        return DataReturn('删除失败或资源不存在', -100);
    }

    /**
     * 状态更新
     * @author   Devil
     * @blog     http://1000-x.cn/
     * @version  0.0.1
     * @datetime 2016-12-06T21:31:53+0800
     * @param    [array]          $params [输入参数]
     */
    public static function StatusUpdate($params = [])
    {
        // 请求参数
        $p = [
            [
                'checked_type' => 'empty',
                'key_name' => 'id',
                'error_msg' => '操作id有误',
            ],
            [
                'checked_type' => 'in',
                'key_name' => 'state',
                'checked_data' => [0, 1, 2],
                'error_msg' => '状态有误',
            ],
        ];
        $ret = ParamsChecked($params, $p);
        if ($ret !== true) {
            return DataReturn($ret, -1);
        }

        $params['state'] == 0 && $params['state'] = 2;

        // 数据更新
        if (Db::name('Home')->where(['id' => intval($params['id'])])->update(['is_enable' => intval($params['state'])])) {
            return DataReturn('编辑成功');
        }
        return DataReturn('编辑失败或数据未改变', -100);
    }

    /**
     * 类型
     * @param $value
     * @return array
     */
    public static function showType($value = -1)
    {
        $status = [1 => '置顶视频', 2 => '商品', 4 => '联系方式'];
//        $status = [1 => '置顶视频', 2 => '商品', 3 =>, 4 => '联系方式'];
        if ($value == -1) {
            return $status;
        }
        if (in_array($value, [4, 2, 1])) return ['text' => $status[$value], 'value' => $value];
        return ['text' => '', 'value' => $value];
    }

    /**
     * 类型
     * @param $value
     * @return array
     */
    public static function isEnable($value = -1)
    {
        $status = [1 => '开启', 2 => '关闭'];
        if (in_array($value, [2, 1])) return ['text' => $status[$value], 'value' => $value];
        if ($value == -1) {
            return $status;
        }
        return ['text' => '', 'value' => $value];
    }

    public static function formatTime($value)
    {
        if ($value > 0) return date('Y-m-d H:i:s', $value);
        return '';

    }
}
