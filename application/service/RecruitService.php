<?php
/**
 *
 * Created by PhpStorm.
 * User: spirit_cjw@163.com
 * Date: 2020/4/9
 * Time: 9:28
 */

namespace app\service;


use think\Db;

class RecruitService
{
    /**
     * 获取招聘列表
     * @param   [array]          $params [输入参数]
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author   Devil
     * @blog    http://1000-x.cn/
     * @version 1.0.0
     * @date    2018-08-29
     * @desc    description
     */
    public static function RecruitList($params)
    {
        $where = empty($params['where']) ? [] : $params['where'];
        $field = empty($params['field']) ? '*' : $params['field'];
        $m = isset($params['m']) ? intval($params['m']) : 0;
        $n = isset($params['n']) ? intval($params['n']) : 10;

        $data = Db::name('Recruit')
            ->field($field)
            ->where($where)
            ->order('id desc')
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

                // 内容
                if(isset($v['content']))
                {
                    $v['content'] = ResourcesService::ContentStaticReplace($v['content'], 'get');
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
     * 招聘总数
     *
     * @method
     * @param $where
     * @return int
     * @author: chen
     * @time: 2020/4/9 11:35
     */
    public static function RecruitTotal($where)
    {
        return (int) Db::name('Recruit')->where($where)->count();
    }

    /**
     * 列表条件
     *
     * @method
     * @param array $params
     * @return array
     * @author: chen
     * @time: 2020/4/9 11:35
     */
    public static function RecruitListWhere($params = [])
    {
        $where = [];

        if(!empty($params['keywords']))
        {
            $where[] = ['title', 'like', '%'.$params['keywords'].'%'];
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
     * 招聘保存
     *
     * @method
     * @param array $params
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @author: chen
     * @time: 2020/4/9 11:34
     */
    public static function RecruitSave($params = [])
    {
        // 请求类型
        $p = [
            [
                'checked_type'      => 'length',
                'key_name'          => 'title',
                'checked_data'      => '2,60',
                'error_msg'         => '标题长度 2~60 个字符',
            ],
            [
                'checked_type'      => 'empty',
                'key_name'          => 'address',
                'error_msg'         => '工作地点不能为空',
            ],
            [
                'checked_type'      => 'fun',
                'key_name'          => 'jump_url',
                'checked_data'      => 'CheckUrl',
                'is_checked'        => 1,
                'error_msg'         => '跳转url地址格式有误',
            ],
            [
                'checked_type'      => 'length',
                'key_name'          => 'content',
                'checked_data'      => '10,105000',
                'error_msg'         => '内容 10~105000 个字符',
            ],
        ];
        $ret = ParamsChecked($params, $p);
        if($ret !== true)
        {
            return DataReturn($ret, -1);
        }

        // 编辑器内容
        $content = isset($params['content']) ? htmlspecialchars_decode($params['content']) : '';

        // 数据
        $data = [
            'title'                 => $params['title'],
            'address'              => $params['address'],
            'title_color'           => empty($params['title_color']) ? '' : $params['title_color'],
            'jump_url'              => empty($params['jump_url']) ? '' : $params['jump_url'],
            'content'               => ResourcesService::ContentStaticReplace($content, 'add'),
            'is_enable'             => isset($params['is_enable']) ? intval($params['is_enable']) : 0,
        ];

        if(empty($params['id']))
        {
            $data['add_time'] = time();
            if(Db::name('Recruit')->insertGetId($data) > 0)
            {
                return DataReturn('添加成功', 0);
            }
            return DataReturn('添加失败', -100);
        } else {
            $data['upd_time'] = time();
            if(Db::name('Recruit')->where(['id'=>intval($params['id'])])->update($data))
            {
                return DataReturn('编辑成功', 0);
            }
            return DataReturn('编辑失败', -100);
        }
    }

    /**
     * 招聘访问统计加1
     *
     * @method
     * @param array $params
     * @return bool|int|true
     * @throws \think\Exception
     * @author: chen
     * @time: 2020/4/9 11:53
     */
    public static function RecruitAccessCountInc($params = [])
    {
        if(!empty($params['id']))
        {
            return Db::name('Recruit')->where(array('id'=>intval($params['id'])))->setInc('access_count');
        }
        return false;
    }

    /**
     * 删除
     *
     * @method
     * @param array $params
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @author: chen
     * @time: 2020/4/9 11:51
     */
    public static function RecruitDelete($params = [])
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
        if(Db::name('Recruit')->where(['id'=>$params['id']])->delete())
        {
            return DataReturn('删除成功');
        }

        return DataReturn('删除失败或资源不存在', -100);
    }

    /**
     * 状态更新
     *
     * @method
     * @param array $params
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @author: chen
     * @time: 2020/4/9 11:52
     */
    public static function RecruitStatusUpdate($params = [])
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
        if(Db::name('Recruit')->where(['id'=>intval($params['id'])])->update([$params['field']=>intval($params['state'])]))
        {
            return DataReturn('编辑成功');
        }
        return DataReturn('编辑失败或数据未改变', -100);
    }

}