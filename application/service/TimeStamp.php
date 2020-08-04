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


/**
 * 时间
 * @author   Devil
 * @blog     http://1000-x.cn/
 * @version  0.0.1
 * @datetime 2016-12-01T21:51:08+0800
 */
class TimeStamp
{
    /**
     * 根据用户搜索时间返回数据分析格式
     * @param string $start
     * @param string $end
     * @param string $type 参数 [month, week, day]
     * @return array
     */
    public static function search($start = '', $end = '', $type = 'month')
    {
        // 自定义时间
        if ($start && $end) {
            $start_time = strtotime($start);
            $end_time = strtotime($end);

            // 一天
        } else if ($type == 'day') {
            $start_time = mktime(0, 0, 0); // 今天00:00
            $end_time = mktime(23, 59, 59); // 今天结束

            // 一星期
        } else if ($type == 'week') {
            $start_time = mktime(0, 0, 0, date('m'), date('d') - 7); // -7天00:00
            $end_time = mktime(0, 0, 0) - 1; // 今天00:00 减1

            // 一个月
        } else {
            $start_time = mktime(0, 0, 0, date('m') - 1, date('d')); // -1个月前00:00
            $end_time = mktime(0, 0, 0) - 1; // 今天00:00 减1
        }

        return [
            'code' => 200,
            'msg' => '',
            'time' => [$start_time, $end_time],
            'format' => [date('Y/m/d H:i:s', $start_time), date('Y/m/d H:i:s', $end_time)],
            'coordinates' => self::getDateByTimeInterval($start_time, $end_time),
        ];
    }

    /**
     * 获取时间索引（坐标）
     * @param  $start
     * @param  $end
     * @return array
     */
    public static function getDateByTimeInterval($start, $end)
    {
        // ['today'=>'G', 'year' => 'Y-m', 'week' => 'n-j', 'month' => 'n-j']; G 24小时制无前缀0， n-j当月第几天无前缀0
        $array_date = [];
        while ($start < $end) {
            $array_date[date('n/j', $start)] = 0;
            $start = strtotime("+1 day", $start);
        }
        return $array_date;
    }

    /**
     * 今天时间戳
     * @return array
     */
    public static function today()
    {
        $start = mktime(0, 0, 0);                       // 今天开始
        $end = mktime(23, 59, 59); // 今天结束
        $date = ['time' => [$start, $end], 'format' => [date('Y-m-d H:i:s', $start), date('Y-m-d H:i:s', $end)]];
        return $date;
    }

    /**
     * 本周时间戳
     * @return array
     */
    public static function thisWeek()
    {
        $start = mktime(0, 0, 0, date('m'), date('d') - date('w') + 1);
        $end = mktime(0, 0, 0, date('m'), date('d') - date('w') + 8) - 1;
        $date = ['time' => [$start, $end], 'format' => [date('Y-m-d H:i:s', $start), date('Y-m-d H:i:s', $end)]];
        return $date;
    }

    /**
     * 本月时间戳
     * @return array
     */
    public static function thisMonth()
    {
        $start = mktime(0, 0, 0, date('m'), 1);      // 本月开始
        $end = mktime(0, 0, 0, date('m') + 1, 1) - 1;  // 本月结束
        $date = ['time' => [$start, $end], 'format' => [date('Y-m-d H:i:s', $start), date('Y-m-d H:i:s', $end)]];
        return $date;
    }


}
