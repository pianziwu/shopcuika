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

class TrecaShopService
{
    /**
     * 获取门店列表
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
    public static function TrecaShopList($params)
    {
        $where = empty($params['where']) ? [] : $params['where'];
        $field = empty($params['field']) ? '*' : $params['field'];
        $m = isset($params['m']) ? intval($params['m']) : 0;
        $n = isset($params['n']) ? intval($params['n']) : 10;

        $data = Db::name('TrecaShop')
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
                if(isset($v['address']))
                {
                    $address = static::getRegion($v['province'], $v['city'], $v['county']);
                    $v['shop_address'] = $address . $v['address'];
                }

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
     * 门店总数
     *
     * @method
     * @param $where
     * @return int
     * @author: chen
     * @time: 2020/4/9 11:35
     */
    public static function TrecaShopTotal($where)
    {
        return (int) Db::name('TrecaShop')->where($where)->count();
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
    public static function TrecaShopListWhere($params = [])
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
                $where[] = ['is_enable', '=', (int)($params['is_enable'])];
            }
            if(isset($params['city']))
            {
                $where[] = ['city', '=', (int)($params['city'])];
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
     * 门店保存
     *
     * @method
     * @param array $params
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @author: chen
     * @time: 2020/4/9 11:34
     */
    public static function TrecaShopSave($params = [])
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
                'key_name'          => 'province',
                'error_msg'         => '请选择省份',
            ],
            [
                'checked_type'      => 'empty',
                'key_name'          => 'city',
                'error_msg'         => '请选择城市',
            ],
            [
                'checked_type'      => 'empty',
                'key_name'          => 'address',
                'error_msg'         => '详细地址不能为空',
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
        $data = [
            'title'                 => $params['title'],
            'province'              => $params['province'],
            'city'                  => $params['city'],
            'county'                => $params['county'] ?? 0,
            'address'               => $params['address'],
            'title_color'           => empty($params['title_color']) ? '' : $params['title_color'],
            'jump_url'              => empty($params['jump_url']) ? '' : $params['jump_url'],
            'is_enable'             => isset($params['is_enable']) ? intval($params['is_enable']) : 0,
        ];

        if(empty($params['id']))
        {
            $data['add_time'] = time();
            if(Db::name('TrecaShop')->insertGetId($data) > 0)
            {
                return DataReturn('添加成功', 0);
            }
            return DataReturn('添加失败', -100);
        } else {
            $data['upd_time'] = time();
            if(Db::name('TrecaShop')->where(['id'=>intval($params['id'])])->update($data))
            {
                return DataReturn('编辑成功', 0);
            }
            return DataReturn('编辑失败', -100);
        }
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
    public static function TrecaShopDelete($params = [])
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
        if(Db::name('TrecaShop')->where(['id'=>$params['id']])->delete())
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
    public static function TrecaShopStatusUpdate($params = [])
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
        if(Db::name('TrecaShop')->where(['id'=>intval($params['id'])])->update([$params['field']=>intval($params['state'])]))
        {
            return DataReturn('编辑成功');
        }
        return DataReturn('编辑失败或数据未改变', -100);
    }

    /**
     * 返回所有门店省市id
     *
     * @method
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author: chen
     * @time: 2020/4/11 15:08
     */
    public static function get_all_shop_region()
    {
        $list = Db::name('TrecaShop')
            ->field(['province', 'city'])
            ->where(['is_enable' => 1])
            ->select();
        if (!empty($list)) {
            $province = array_column($list, 'province');
            $city = array_column($list, 'city');
            return array_merge($province, $city);
        }
        return [];
    }

    /**
     * 返回门店地址
     *
     * @method
     * @param int $province
     * @param int $city
     * @param int $county
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author: chen
     * @time: 2020/4/9 15:51
     */
    private static function getRegion($province = 0, $city = 0, $county = 0)
    {
        $region = Db::name('Region')
            ->field(['id', 'name'])
            ->where([
                ['id', 'in', [$province, $city, $county]]
            ])
            ->select();
        $data = [];
        if (!empty($region)) {
            foreach ($region as $value) {
                $data[$value['id']] = $value['name'];
            }
        }
        $province_name = $data[$province] ?? '';
        $city_name = $data[$city] ?? '';
        $county_name = $data[$county] ?? '';
        return $province_name . $city_name . $county_name;
    }

}