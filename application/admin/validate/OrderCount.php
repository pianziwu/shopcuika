<?php
/**
 * Created by yang
 * User: bonzaphp@gmail.com
 * Date: 2020-04-17
 * Time: 09:33
 */

namespace app\admin\validate;

use think\Validate;

class OrderCount extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'start_time'   => ['date'],
        'end_time'     => ['date'],
        'first_month'  => ['date'],
        'second_month' => ['date'],
        'third_month'  => ['date'],
        'forth_month'  => ['date'],
        'five_month'   => ['date'],
    ];
    /**
     * 验证错误信息
     * @var array
     */
    protected $message = [
        'start_time.date' => '开始时间格式错误',
        'end_time.date'   => '结束时间格式错误',
    ];
    /**
     * 验证场景
     * @var array
     */
    protected $scene = [
        'index' => [
            'start_time', 'end_time', 'first_month', 'second_month', 'third_month', 'forth_month', 'five_month'
        ],
    ];
}