<?php
/**
 *
 * Created by PhpStorm.
 * User: spirit_cjw@163.com
 * Date: 2020/4/18
 * Time: 16:24
 */

namespace app\service;


use think\Db;

class ChronicleService
{
    /**
     * 获取大事记列表
     * @param   [array]          $params [输入参数]
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @version 1.0.0
     * @date    2018-08-29
     * @desc    description
     * @author   Devil
     * @blog    http://1000-x.cn/
     */
    public static function ChronicleList($params)
    {
        $where = empty($params['where']) ? [] : $params['where'];
        $field = empty($params['field']) ? '*' : $params['field'];
        $m = isset($params['m']) ? intval($params['m']) : 0;
        $n = isset($params['n']) ? intval($params['n']) : 10;

        $data = Db::name('Chronicle')
            ->field($field)
            ->where($where)
            ->order('id')
            ->limit($m, $n)
            ->select();

        if(!empty($data))
        {
            $common_is_enable_tips = lang('common_is_enable_tips');
            foreach($data as &$v)
            {
                // 是否启用
                if(isset($v['is_enable']))
                {
                    $v['is_enable_text'] = $common_is_enable_tips[$v['is_enable']]['name'];
                }

                if(isset($v['add_time']))
                {
                    $v['add_time_time'] = date('Y-m-d H:i:s', $v['add_time']);
                    $v['add_time_date'] = date('Y-m-d', $v['add_time']);
                }
                if(isset($v['upd_time']))
                {
                    $v['upd_time_time'] = date('Y-m-d H:i:s', $v['upd_time']);
                    $v['upd_time_date'] = date('Y-m-d', $v['upd_time']);
                }
            }
        }
        return DataReturn('处理成功', 0, $data);
    }

    /**
     * 大事记总数
     * @param    [array]          $where [条件]
     * @return int
     * @author   Devil
     * @blog     http://1000-x.cn/
     * @version  0.0.1
     * @datetime 2016-12-10T22:16:29+0800
     */
    public static function ChronicleTotal($where)
    {
        return (int) Db::name('Chronicle')->where($where)->count();
    }

    /**
     * 列表条件
     * @param array $params
     * @return array
     * @author   Devil
     * @blog    http://1000-x.cn/
     * @version 1.0.0
     * @date    2018-09-29
     * @desc    description
     */
    public static function ChronicleListWhere($params = [])
    {
        $where = [];

        if(!empty($params['keywords']))
        {
            $where[] = ['year', 'like', '%'.$params['keywords'].'%'];
        }

        // 是否更多条件
        if(isset($params['is_more']) && $params['is_more'] == 1)
        {
            // 等值
            if(isset($params['is_enable']) && $params['is_enable'] > -1)
            {
                $where[] = ['is_enable', '=', intval($params['is_enable'])];
            }

            if(!empty($params['time_start']))
            {
                $where[] = ['add_time', '>', strtotime($params['time_start'])];
            }
            if(!empty($params['time_end']))
            {
                $where[] = ['add_time', '<', strtotime($params['time_end'])];
            }
        }

        return $where;
    }

    /**
     * 大事记保存
     * @param array $params
     * @return array|bool|mixed|string
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @author   Devil
     * @blog    http://1000-x.cn/
     * @version 1.0.0
     * @date    2018-12-18
     * @desc    description
     */
    public static function ChronicleSave($params = [])
    {
        // 请求类型
        $p = [
            [
                'checked_type'      => 'length',
                'key_name'          => 'year',
                'checked_data'      => '2,20',
                'error_msg'         => '年份长度 2~20 个字符',
            ],
            [
                'checked_type'      => 'empty',
                'key_name'          => 'synopsis',
                'error_msg'         => '简介不能为空',
            ],
            [
                'checked_type'      => 'empty',
                'key_name'          => 'head_img',
                'error_msg'         => '图片不能为空',
            ],
            [
                'checked_type'      => 'fun',
                'key_name'          => 'jump_url',
                'checked_data'      => 'CheckUrl',
                'is_checked'        => 1,
                'error_msg'         => '跳转url地址格式有误',
            ],
        ];
        $ret = ParamsChecked($params, $p);
        if($ret !== true)
        {
            return DataReturn($ret, -1);
        }

        // 数据
        $head_img = parse_url($params['head_img']);
        $data = [
            'year'      => $params['year'],
            'synopsis'  => $params['synopsis'],
            'head_img'  => $head_img['path'] ?? '',
            'jump_url'  => empty($params['jump_url']) ? '' : $params['jump_url'],
            'is_enable' => isset($params['is_enable']) ? intval($params['is_enable']) : 0,
        ];

        if(empty($params['id']))
        {
            $data['add_time'] = time();
            if(Db::name('Chronicle')->insertGetId($data) > 0)
            {
                return DataReturn('添加成功', 0);
            }
            return DataReturn('添加失败', -100);
        } else {
            $data['upd_time'] = time();
            if(Db::name('Chronicle')->where(['id'=>intval($params['id'])])->update($data))
            {
                return DataReturn('编辑成功', 0);
            }
            return DataReturn('编辑失败', -100);
        }
    }

    /**
     * 删除
     * @param array $params
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @author   Devil
     * @blog    http://1000-x.cn/
     * @version 1.0.0
     * @date    2018-12-18
     * @desc    description
     */
    public static function ChronicleDelete($params = [])
    {
        // 请求参数
        $p = [
            [
                'checked_type'      => 'empty',
                'key_name'          => 'id',
                'error_msg'         => '操作id有误',
            ],
        ];
        $ret = ParamsChecked($params, $p);
        if($ret !== true)
        {
            return DataReturn($ret, -1);
        }

        // 删除操作
        if(Db::name('Chronicle')->where(['id'=>$params['id']])->delete())
        {
            return DataReturn('删除成功');
        }

        return DataReturn('删除失败或资源不存在', -100);
    }

    /**
     * 状态更新
     * @param array $params
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @author   Devil
     * @blog     http://1000-x.cn/
     * @version  0.0.1
     * @datetime 2016-12-06T21:31:53+0800
     */
    public static function ChronicleStatusUpdate($params = [])
    {
        // 请求参数
        $p = [
            [
                'checked_type'      => 'empty',
                'key_name'          => 'id',
                'error_msg'         => '操作id有误',
            ],
            [
                'checked_type'      => 'empty',
                'key_name'          => 'field',
                'error_msg'         => '操作字段有误',
            ],
            [
                'checked_type'      => 'in',
                'key_name'          => 'state',
                'checked_data'      => [0,1],
                'error_msg'         => '状态有误',
            ],
        ];
        $ret = ParamsChecked($params, $p);
        if($ret !== true)
        {
            return DataReturn($ret, -1);
        }

        // 数据更新
        if(Db::name('Chronicle')->where(['id'=>intval($params['id'])])->update([$params['field']=>intval($params['state'])]))
        {
            return DataReturn('编辑成功');
        }
        return DataReturn('编辑失败或数据未改变', -100);
    }

}
?>